<?php

namespace App\Observers;

class QaObserver extends ObServer
{
    public function created()
    {
        $this->forgetCache('qas');
    }

    public function updated()
    {
        $this->forgetCache('qas');
    }

    public function deleted()
    {
        $this->forgetCache('qas');
    }

    public function restored()
    {
        $this->forgetCache('qas');
    }

    public function forceDeleted()
    {
        $this->forgetCache('qas');
    }
}
