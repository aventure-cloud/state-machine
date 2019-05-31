<?php

namespace Aventure\StateMachine;

use Aventure\StateMachine\Events\TransitionEvent;
use Aventure\StateMachine\Exceptions\StateMachineException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class StateMachine
{
    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var array
     */
    protected $transitions;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * StateMachine constructor.
     *
     * @param \stdClass $object
     * @param array $config
     */
    public function __construct($object, array $config)
    {
        $this->object = $object;

        if (!isset($config['property_path'])) {
            $config['property_path'] = 'state';
        }

        $this->config = $config;

        $this->propertyAccessor = new PropertyAccessor();
    }

    /**
     * Returns the possible transitions.
     *
     * @return array
     */
    public function getPossibleTransitions() : array
    {
        return array_filter(
            array_keys($this->config['transitions']),
            [$this, 'can']
        );
    }

    /**
     * Can the transition be applied on the underlying object.
     *
     * @param string $transition
     * @return bool
     * @throws StateMachineException If transition doesn't exist
     */
    public function can($transition) : bool
    {
        if (!isset($this->config['transitions'][$transition])) {
            throw new StateMachineException(sprintf(
                'Transition "%s" does not exist on object "%s"',
                $transition,
                get_class($this->object)
            ));
        }

        $from = is_string($this->config['transitions'][$transition]['from'])
            ? [ $this->config['transitions'][$transition]['from'] ] // wrap in array
            : $this->config['transitions'][$transition]['from'];

        return in_array($this->getState(), $from);
    }

    /**
     * Applies the transition on the underlying object.
     *
     * @param $transition
     * @return bool
     * @throws StateMachineException
     * @throws \TypeError
     */
    public function apply($transition) : bool
    {
        if (!$this->can($transition)) {
            throw new StateMachineException("Transition {$transition} not allowed");
        }

        event(
            'state_machine.transitioning.'.$this->getObjectName().'.'.$transition,
            new TransitionEvent($this->object, $transition)
        );

        $this->setState($this->config['transitions'][$transition]['to']);

        event(
            'state_machine.transited.'.$this->getObjectName().'.'.$transition,
            new TransitionEvent($this->object, $transition)
        );

        return true;
    }

    /**
     * Get current state value of object.
     */
    public function getState()
    {
        return $this->propertyAccessor->getValue($this->object, $this->config['property_path']);
    }

    /**
     * Set a new state to the underlying object.
     *
     * @param string $state
     * @throws StateMachineException
     * @throws \TypeError
     */
    protected function setState($state)
    {
        if (!in_array($state, $this->config['states'])) {
            throw new StateMachineException(sprintf(
                'Cannot set the state to "%s" to object "%s" with graph %s because it is not pre-defined.',
                $state,
                get_class($this->object),
                $this->config['graph']
            ));
        }

        $this->propertyAccessor->setValue(
            $this->object,
            $this->config['property_path'],
            $state
        );
    }

    /**
     * Retrieve object class name
     *
     * @return string
     */
    protected function getObjectName()
    {
        return strtolower(class_basename($this->object));
    }
}
