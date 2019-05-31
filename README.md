# Laravel State Machine


[![Latest Stable Version](https://poser.pugx.org/ilvalerione/state-machine/v/stable)](https://packagist.org/packages/ilvalerione/state-machine)
[![Total Downloads](https://poser.pugx.org/ilvalerione/state-machine/downloads)](https://packagist.org/packages/ilvalerione/state-machine)
[![License](https://poser.pugx.org/ilvalerione/state-machine/license)](https://packagist.org/packages/ilvalerione/state-machine)

- **Author:** Valerio Barbera - [support@logengine.dev](mailto:support@logengine.dev)
- **Author Website:** [www.logengine.dev](target="_blank":https://www.logengine.dev) 


State machine for Laravel based applications.


## Install
``` composer require ilvalerione/state-machine ```

## Config
``` php artisan vendor:publish --provider="Aventure\StateMachine\ServiceProvider" ```

This command publish a new configuration file in your `config` directory
to list all state graphs that you want use in your application.

The default config file ships with a complete graph example.

## Eloquent model integration
Add `HasStateMachine` trait to the eloquent model which you want manage its state:

```php
class Order extends Model
{
    use HasStateMachine;
    
    ...
```


## Assign state machine configuration to the model

```php
class Order extends Model
{
    use HasStateMachine;

    /**
     * StateMachine configuration
     *
     * @return array
     */
    protected function stateMachineConfig() : array
    {
	    // This is the name of the graph in the "state-machine.php" config file
        return [
            'graph' => 'order',
        ];
    }
}
```


## Default status
Use the eloquent `$attributes` property to set the default status of your object:

```php
class Order extends Model
{
    use HasStateMachine;
	
    /**
     * Default model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending'
    ];

    /**
     * StateMachine configuration
     *
     * @return array
     */
    protected function stateMachineConfig() : array
    {
	    // This is the name of the graph in the "state-machine.php" config file
        return [
            'graph' => 'order',
        ];
    }
}
```


## Events
StateMachine provide you two main hooks to control the transition between states:
- Transitioning
- Transited

to point before and after every status change.

In the example below in your `EventServiceProvider` you can point before and after Order's "accept" transition.

```php
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
		'state_machine.transitioning.order.accept' => [
           ListenBeforeOrderIsAccepted::class,
        ],

        'state_machine.transited.order.accept' => [
            ListenAfterOrderIsAccepted::class,
        ],
    ];
}
```

The name of the events is dynamically composed using the convention below:

```
// Before trantion execution
state_machine.transitioning.[class_name].[transition_name]

// After trasition is executed
state_machine.transited.[model_class_name].[transition_name]
```

In this way you can attach your listener to a specific object when a particular transition happen.


# LICENSE
This package are licensed under the [MIT](LICENSE) license.
