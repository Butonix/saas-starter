<?php

Nova::routes()
    ->withAuthenticationRoutes()
    ->withPasswordResetRoutes()
    ->register();
