protected $routeMiddleware = [
    // ... other middleware
    'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
];