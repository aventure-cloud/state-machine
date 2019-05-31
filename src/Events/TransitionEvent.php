<?php

namespace Aventure\StateMachine\Events;

use Illuminate\Queue\SerializesModels;

class TransitionEvent
{
    use SerializesModels;

    /**
     * Name of the transition being applied.
     *
     * @var string
     */
    public $transition;

    /**
     * Config of the transition.
     *
     * @var \stdClass
     */
    public $object;

    /**
     * TransitionEvent constructor.
     *
     * @param mixed $object
     * @param string $transition
     */
    public function __construct($object, $transition = null)
    {
        $this->object = $object;
        $this->transition = $transition;
    }
}
