<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */
use Respect\Validation\Validator as v;


// Register provider
$container['apiValidation'] = function () {
    //Create the validators
    $usernameValidator = v::alnum()->noWhitespace()->length(1, 10);
    $ageValidator = v::numeric()->positive()->between(1, 20);
    $validators = array(
        'username' => $usernameValidator,
        'age' => $ageValidator
    );

    return new Decorate\Validation\Validation($validators);
};

// ******************************** Start Passport Api ************************
$app->group('/passport/v1/', function () {
    $this->post('user/login', 'Passport\Services\UserService:login');
})->add($container['apiValidation']);
// ******************************** End Passport Api **************************

// ******************************** Start Decorate Api ************************
$app->group('/decorate/v1/', function () {
    $this->post('diary/add', 'Decorate\Services\DiaryService:add');
    $this->post('diary/get', 'Decorate\Services\DiaryService:getDiaryDetailById');
});
// ******************************** End Decorate Api **************************