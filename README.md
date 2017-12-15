# Web2All signal

This package can be used to easily enable signal handling in your commandline tool or daemon.

## What does it do ##

Catch signals sent to your program and allow you to handle them. By default catch `kill -HUP`, `kill` and `ctrl-c`.
It is intended for simple usage so you can gracefully shutdown your program.

## Usage ##

    $sh = new Web2All_Signal_Handler();
    $sh->pcntlSignalSetup();
    while(1){
      // do stuff
      sleep($time);
      // you must call pcntlSignalDispatch regulary (preferably after each sleep)
      // so all signals get handled. Signals will automatically interrupt the
      // php sleep() command.
      $sh->pcntlSignalDispatch();
      if ($sh->pcntlSignalReceived()) break;
    }

## License ##

Web2All framework is open-sourced software licensed under the MIT license ([https://opensource.org/licenses/MIT](https://opensource.org/licenses/MIT "license")).
