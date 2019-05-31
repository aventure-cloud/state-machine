<?php

namespace Aventure\StateMachine\Traits;

use Aventure\StateMachine\Exceptions\StateMachineException;
use Aventure\StateMachine\StateMachine;

trait HasStateMachine
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    /**
     * Obtain the state machine instance
     *
     * @return StateMachine
     */
    public function stateMachine()
    {
        if (!$this->stateMachine) {
            $this->stateMachine = new StateMachine($this, $this->getStatesGraph());
        }
        return $this->stateMachine;
    }

    /**
     * Get states configuration graph
     *
     * @return array|\Illuminate\Config\Repository|mixed
     */
    protected function getStatesGraph()
    {
        return method_exists($this, 'stateMachineConfig')
            ? config('state-machine.'.$this->stateMachineConfig()['graph'])
            : [];
    }

    /**
     * Apply transition
     *
     * @param string $transition
     * @throws \App\StateMachine\Exceptions\StateMachineException
     * @throws \TypeError
     * @throws StateMachineException
     */
    public function transition(string $transition)
    {
        if (! $this->stateMachine()->apply($transition)) {
            throw new StateMachineException("StateMachine fails to apply transition {$transition}");
        }

        $this->save();
    }
}
