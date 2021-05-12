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
    public static $STATUS_CODE_ALREADY_EXISTS = "data_already_exists";




    public static $MESSAGE_AUTHENTICATED = "Выполнена авторизация!";
    public static $MESSAGE_HAS_VALIDATION_ERRORS = "Вы ввели неправильные поля!";
    public static $MESSAGE_LOGIN_INCORRECT = "Неверный логин или пароль!";
    public static $MESSAGE_VERIFY_EMAIL = "Пожалуйста, подтвердите почту";
    public static $MESSAGE_VERIFY_EMAIL_SEND = "Ссылка для подтверждение почты, отправлано на вашу почту!";
    public static $MESSAGE_EMAIL_VERIFIED_ALREADY = "Ваша почта уже подтверждена, попробуйте залогиниться!";
    public static $MESSAGE_EMAIL_VERIFIED = "Ваша почта успешно подтверждена!";
    public static $MESSAGE_EMAIL_NOT_FOUND = "Указанная почта не найдена в нашем базе данных!";
    public static $MESSAGE_USER_DEFINED_AS_TEACHER = "Пользователь успешно зарегестрировался как учитель!";

    public static $MESSAGE_PROFILE_NOT_COMPLETED = "Профиль тичера не заполнена!";
    public static $MESSAGE_USER_LOGOUT = "Вы вышли из системы!";
    public static $MESSAGE_USER_PROFILE_UPDATED = "Ваша профиль обновлена!";


    public static $MESSAGE_COURSE_UPLOADED_SUCCESS = "Гуд джоб!";
    public static $MESSAGE_SMTH_DELETED = "Успешно удалена из базы!";
    public static $MESSAGE_SUCCESS_ADDED = "Успешно добавлена в базу!";
    public static $MESSAGE_DATA_NOT_FOUND = "То что вы ищете не существует!";
    public static $MESSAGE_ALREADY_EXISTS = "Уже существует в Базе!";

    public static $MESSAGE_DATA_HAS_BEEN_MODIFIED = "Данные обнавлены!";
//    public static $MESSAGE_USER_PROFILE_UPDATED = "Ваша профиль обновлена!";
}
