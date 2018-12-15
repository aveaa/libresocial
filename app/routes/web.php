<?php
use Unionity\Maximizer\Routing\Route;

Route::setGlobalNamespace("\Unionity\OpenVK4");
Route::setGlobalMiddlewares(["Auth", "AntiCSRF"]);

Route::get("/", "IndexController#index");

Route::get("/login", "AuthController#login");

Route::get("/signup", "AuthController#signup");

Route::get("/logout", "AuthController#unauthorize", ["ForceAuth"]);

Route::get("/revoke_token", "AuthController#revoke", ["ForceAuth"]);

Route::post("/login", "AuthController#authorize");

Route::post("/signup", "AuthController#register");

Route::get("/feed", "PostController#feed", ["ForceAuth"]);

Route::get("/id([0-9]+)", "UserController#view");

Route::get("/public([0-9]+)", "ClubController#view");

Route::get("/settings", "UserController#edit", ["ForceAuth"]);

Route::post("/settings", "UserController#edit", ["ForceAuth"]);

Route::post("/feed", "PostController#post", ["ForceAuth"]);

Route::post("/like/([A-z])/([0-9]+)", "PostController#like", ["ForceAuth"]);

Route::post("/rem/([A-z])/([0-9]+)", "PostController#remove", ["ForceAuth"]);

Route::post("/ed/([A-z])/([0-9]+)", "PostController#edit", ["ForceAuth"]);

Route::post("/comments/([0-9]+)", "PostController#comment", ["ForceAuth"]);

Route::get("/friends", "SubController#friends", ["ForceAuth"]);

Route::get("/groups", "SubController#groups", ["ForceAuth"]);

Route::get("/gedit", "ClubController#control", ["ForceAuth"]);

Route::post("/gedit", "ClubController#edit", ["ForceAuth"]);

Route::post("/sub/([-]?[0-9]+)", "SubController#sub", ["ForceAuth"]);

Route::post("/unsub/([-]?[0-9]+)", "SubController#unsub", ["ForceAuth"]);
