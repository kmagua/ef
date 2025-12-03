<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\components;

use Yii;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\web\NotFoundHttpException; 
use app\modules\backend\models\AuditTrail;

/**
 * Description of AuditTrailLogger
 *
 * @author nkalu
 */


class AuditTrailLogger extends \yii\base\Behavior{
    
    const AUDIT_TYPE_INSERT = 'SET';
    const AUDIT_TYPE_UPDATE = 'CHANGE';
    const AUDIT_TYPE_DELETE = 'DELETE';
public $ignoredAttributes = ['profile_photo'];
    public static $PARENT_MAP = [
        'Result' => [
            'key' => 'fk_test',
            'model' => 'Test'
            ],
        'IsolateResult' => [
            'key' => 'fk_test',
            'model' => 'Test'
            ],
        'PcrResult' => [
            'key' => 'fk_lab_extract',
            'model' => 'Extract'
            ],
        'Test' => [
            'key' => 'fk_specimen',
            'model' => 'Specimen'
            ],
        'Isolate' => [
            'key' => 'fk_specimen',
            'model' => 'Specimen'
            ],
        'Extract' => [
            'key' => 'fk_specimen',
            'model' => 'specimen'
            ],
        'Specimen' => [
            'key' => 'fk_receive_specimen',
            'model' => 'SampleReceive'
            ],
        'SampleReceive' => [
            'key' => 'fk_study',
            'model' => 'StudyList'
            ],
        ];
    
    /**
	 * @var string[] holds all allowed audit types
	 */
	public static $AUDIT_TYPES = [self::AUDIT_TYPE_INSERT, self::AUDIT_TYPE_UPDATE, self::AUDIT_TYPE_DELETE];
	/**
	 * @var string[] if defined, the listed attributes will be ignored. Good examples for
	 * fields to ignore would be the db-fields of TimestampBehavior or BlameableBehavior.
	 */
	
    /**
	 * @var \Closure|null optional closure to return the timestamp of an event. It needs to be
	 * in the format 'function() { }' returning an integer. If not set 'time()' is used.
	 */
	public $timestampCallback = null;
	/**
	 * @var integer|\Closure|null the user id to use if console actions modify a model.
	 * If a closure is used, use the 'function() { }' and return an integer or null.
	 */
	public $consoleUserId = null;
	/**
	 * @var boolean if set to true, the data fields will be persisted upon insert. Defaults to true.
	 */
	public $persistValuesOnInsert = true;
	/**
	 * @var bool if this is set to true, a change to an empty string value will be logged as null
	 */
	public $emptyStringIsNull = true;
	/**
	 * @var bool whether or not to compare strings in a case sensitive way to detect changes (default: false)
	 */
	public $caseSensitive = false;
	/**
	 * @var boolean if set to true, inserts will be logged (default: true)
	 */
	public $logInsert = true;
	/**
	 * @var boolean if set to true, updates will be logged (default: true)
	 */
	public $logUpdate = true;
	/**
	 * @var boolean if set to true, deletes will be logged (default: true)
	 */
	public $logDelete = true;
    
    /**
	 * @inheritdoc
	 */

    public function attach($owner)
	{
		//assert owner extends class ActiveRecord
		if (!($owner instanceof ActiveRecord)) {
			throw new InvalidConfigException('AuditTrailBehavior can only be applied to classes extending \yii\db\ActiveRecord');
		}
		parent::attach($owner);
	}
	/**
	 * @inheritdoc
	 */
	public function events()
	{
		return [
			//ActiveRecord::EVENT_AFTER_INSERT  => 'onAfterInsert',
			ActiveRecord::EVENT_AFTER_UPDATE  => 'onAfterUpdate',
			ActiveRecord::EVENT_BEFORE_DELETE => 'onBeforeDelete',
		];
	}
    
    /**
	 * Handler for after insert event
	 *
	 * @param \yii\db\AfterSaveEvent $event the after save event
	 */
	public function onAfterInsert($event)
	{
		if (!$this->logInsert) return;
		
		//if configured write initial values
		if ($this->persistValuesOnInsert) {
			foreach ($this->getRelevantDbAttributes() as $attrName) {
                $entry = $this->createPreparedAuditTrailEntry(self::AUDIT_TYPE_INSERT);
				$newVal = $this->owner->{$attrName};
				//catch null values
				if ($newVal === null) continue;
				//catch empty strings
				if ($this->emptyStringIsNull && is_string($newVal) && empty($newVal)) {
					continue;
				}
				$entry->addChange($attrName, null, $newVal);
			}
		}
		static::saveEntry($entry);
	}
	/**
	 * Handler for after update event
	 *
	 * @param \yii\db\AfterSaveEvent $event the after save event
	 */
	public function onAfterUpdate($event)
	{
		if (!$this->logUpdate) return;
		
		//fetch dirty attributes and add changes
		$relevantAttrs = $this->getRelevantDbAttributes();
		foreach ($event->changedAttributes as $attrName=>$oldVal) {
            $entry = $this->createPreparedAuditTrailEntry(self::AUDIT_TYPE_UPDATE);
			//skip if ignored
			if (!in_array($attrName, $relevantAttrs)) continue;
			$newVal = static::castValue($oldVal, $this->owner->{$attrName});
			//additional comparison after casting
			if ((is_string($newVal) && call_user_func($this->caseSensitive ? 'strcmp' : 'strcasecmp', (string)$oldVal , (string)$newVal) === 0) || (string)$oldVal === (string)$newVal) {
				continue;
			}
            if(is_null($newVal)){ $newVal = ''; }
			//catch empty strings
			if ($this->emptyStringIsNull && is_string($newVal) && empty($newVal)) {
				if ($oldVal === null) continue;
				$newVal = null;
			}
			$entry->addChange($attrName, (string)$oldVal, (string)$newVal);
            static::saveEntry($entry);
		}
		
	}
	/**
	 * Handler for before delete event
	 */
	public function onBeforeDelete()
	{
		if (!$this->logDelete) return;
		$entry = $this->createPreparedAuditTrailEntry(self::AUDIT_TYPE_DELETE);
		static::saveEntry($entry);
	}
	/**
	 * Creates and returns a pre-configured audit trail model
	 *
	 * @param string $changeKind the kind of audit trail entry (use this classes statics)
	 * @return \asinfotrack\yii2\audittrail\models\AuditTrailEntry
	 */
	protected function createPreparedAuditTrailEntry($changeKind)
{
    $modelClass = get_class($this->owner);
    $pk         = $this->owner->primaryKey()[0] ?? null;
    $modelId    = $pk ? $this->owner->$pk : null;

    switch ($changeKind) {
        case 'DELETE':
            $entry = new \app\modules\backend\models\AuditTrail([
                'model_id'  => $modelId,
                'model'     => $modelClass,
                'field'     => '*', // placeholder since it's a full record delete
                'old_value' => json_encode($this->owner->attributes),
                'new_value' => null,
                'action'    => 'DELETE',
                'stamp'     => $this->getHappenedAt(),
                'user_id'   => $this->getUserId(),
                'comments'  => 'Record deleted',
            ]);
            break;

        case 'CREATE':
            $entry = new \app\modules\backend\models\AuditTrail([
                'model_id'  => $modelId,
                'model'     => $modelClass,
                'field'     => '*', // placeholder
                'old_value' => null,
                'new_value' => json_encode($this->owner->attributes),
                'action'    => 'CREATE',
                'stamp'     => $this->getHappenedAt(),
                'user_id'   => $this->getUserId(),
                'comments'  => 'Record created',
            ]);
            break;

        case 'UPDATE':
        case 'CHANGE':
            // save one entry for all fields together
            $entry = new \app\modules\backend\models\AuditTrail([
                'model_id'  => $modelId,
                'model'     => $modelClass,
                'field'     => '*', // or could be changed later per field
                'old_value' => isset($this->oldAttributes) ? json_encode($this->oldAttributes) : null,
                'new_value' => json_encode($this->owner->attributes),
                'action'    => 'UPDATE',
                'stamp'     => $this->getHappenedAt(),
                'user_id'   => $this->getUserId(),
                'comments'  => 'Record updated',
            ]);
            break;

        default:
            throw new \InvalidArgumentException("Unsupported changeKind: {$changeKind}");
    }

    return $entry;
}


    
  private function getSessionId()
{
    if (!\Yii::$app->session->has('delete_session')) {
        // fallback if nothing was set in session
        return uniqid('audit_', true);
    }

    return \Yii::$app->session->get('delete_session');
}

	/**
	 * Returns the user id to use for am audit trail entry
	 *
	 * @return integer|null returns either a user id or null.
	 */
	protected function getUserId()
	{
		if (Yii::$app instanceof \yii\console\Application) {
			if ($this->consoleUserId instanceof \Closure) {
				return call_user_func($this->consoleUserId);
			} else {
				return $this->consoleUserId;
			}
		} else if (Yii::$app->user->getIsGuest()) {
			return null;
		} else {
			return Yii::$app->user->identity->id;
		}
	}
	/**
	 * Returns the timestamp for the audit trail entry.
	 *
	 * @return integer unix-timestamp
	 */
	protected function getHappenedAt()
	{
		if ($this->timestampCallback !== null) {
			return call_user_func($this->timestampCallback);
		} else {
			return date('Y-m-d H:i:s');
		}
	}
	/**
	 * Creates the json-representation of the pk (array in the format attribute=>value)
	 * @see \asinfotrack\yii2\toolbox\helpers\PrimaryKey::asJson()
	 *
	 * @return string json-representation of the pk-array
	 * @throws \yii\base\InvalidParamException if the model is not of type ActiveRecord
	 * @throws \yii\base\InvalidConfigException if the models pk is empty or invalid
	 */
	protected function createPrimaryKeyJson()
	{
		return PrimaryKey::asJson($this->owner);
	}
	/**
	 * This method is responsible to create a list of relevant db-columns to track. The ones
	 * listed to exclude will be removed here already.
	 *
	 * @return string[] array containing relevant db-columns
	 */
	protected function getRelevantDbAttributes()
	{
		//get cols from db-schema
		$cols = array_keys($this->owner->getTableSchema()->columns);
		//return if no ignored cols
		if (empty($this->ignoredAttributes)) return $cols;
		//remove ignored cols and return
		$colsFinal = [];
		foreach ($cols as $c) {
			if (in_array($c, $this->ignoredAttributes)) continue;
			$colsFinal[] = $c;
		}
		return $colsFinal;
	}
	/**
	 * Casts the new value into the type of the old value when necessary
	 *
	 * @param mixed $oldVal the old value of which the type is relevant
	 * @param mixed $newVal the newly received value which will be disinfected
	 * @return mixed the type casted into correct type
	 */
	protected static function castValue($oldVal, $newVal)
	{
        return (string)$newVal;
		//handle numerical and boolean values
		/**if (is_string($newVal) && is_numeric($newVal)) {
			if (is_bool($oldVal)) {
				return boolval($newVal);
			}  else if (is_float($oldVal)) {
				return floatval($newVal);
			} else if (is_double($oldVal)) {
				return doubleval($newVal);
			}else if (preg_match('/[0-9]+/', $newVal)) {
				return intval($newVal);
			}
		}
		return $newVal;*/
	}
	/**
	 * Saves the entry and outputs an exception describing the problem if necessary
	 *
	 * @param \asinfotrack\yii2\audittrail\models\AuditTrailEntry $entry
	 * @throws InvalidValueException if entry couldn't be saved (validation error)
	 */
	protected static function saveEntry($entry)
	{
		//do nothing if successful
		if ($entry->save()) return;
		//otherwise throw exception
		$lines = [];
		foreach ($entry->errors as $attr=>$errors) {
			foreach ($errors as $err) $lines[] = $err;
		}
		//throw new InvalidValueException(sprintf('Error while saving audit-trail-entry: %s', implode(', ', $lines)));
	}
    
    protected function getParentDetails($item)
    {
        $class = \yii\helpers\StringHelper::basename(get_class($this->owner));
        switch($item)
        {
            case 'parent_model':
                if(array_key_exists($class, self::$PARENT_MAP)){
                    return self::$PARENT_MAP[$class]['model'];
                }
                break;
            case 'parent_id':
                if(array_key_exists($class, self::$PARENT_MAP)){
                    $att_name = self::$PARENT_MAP[$class]['key'];
                    if(isset($this->owner->$att_name)) {
                        return $this->owner->$att_name;
                    }
                }
                break;
        }
    }


    
}
