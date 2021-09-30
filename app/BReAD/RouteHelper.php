<?php

namespace App\BReAD;

use Route;

class RouteHelper {
    public static function apiResource($link,$controller) {
        Route::get("/$link","$controller@apiIndex");
        Route::get("/$link/{id}","$controller@apiShow");
        Route::get("/$link/{column}/{value}","$controller@apiSearch");
        Route::post("/$link","$controller@apiCreateOrEdit");
        Route::delete("/$link/{id}","$controller@apiDestroy");
    }

    public static function webResource($link,$controller) {
        Route::get("/$link","$controller@index")->name("$link");
        Route::get("/$link/dt","$controller@dtIndex")->name("$link.dt");
    }
}