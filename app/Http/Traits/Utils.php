<?php


namespace App\Http\Traits;


class Utils
{

    public static $SUCCESS_CODE = 200;
//    public static $ERROR_CODE = 400;
    public static $STATUS_CODE_LOGIN_INCORRECT = "incorrect_user_data";
    public static $STATUS_CODE_EMAIL_NOT_VERIFIED = "email_not_verified";
    public static $STATUS_CODE_HAS_INCORRECT_FIELDS = "has_incorrect_fields";
    public static $STATUS_CODE_NOT_FOUND = "data_not_found";
    public static $STATUS_CODE_PROFILE_NOT_COMPLETED = "profile_not_completed";



    public static $MESSAGE_AUTHENTICATED = "Выполнена авторизация!";
    public static $MESSAGE_LOGIN_INCORRECT = "Неверный логин или пароль!";
    public static $MESSAGE_VERIFY_EMAIL = "Пожалуйста, подтвердите почту";
    public static $MESSAGE_VERIFY_EMAIL_SEND = "Ссылка для подтверждение почты, отправлано на вашу почту!";
    public static $MESSAGE_EMAIL_VERIFIED_ALREADY = "Ваша почта уже подтверждена, попробуйте залогиниться!";
    public static $MESSAGE_EMAIL_VERIFIED = "Ваша почта успешно подтверждена!";
    public static $MESSAGE_EMAIL_NOT_FOUND = "Указанная почта не найдена в нашем базе данных!";
    public static $MESSAGE_USER_DEFINED_AS_TEACHER = "Пользователь успешно зарегестрировался как учитель!";

    public static $MESSAGE_PROFILE_NOT_COMPLETED = "Профиль тичера не заполнена!";
}
