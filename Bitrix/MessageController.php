<?php

namespace App\Http\Controllers\Bitrix;

use App\Http\Controllers\Controller;
use App\Jobs\Bitrix\CreateHRMessage;
use App\Models\bitrixAccount;
use App\Models\Organization;
use App\Models\Person;
use App\Services\Bitrix\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessageNewAccount(Person $person)
    {
        $message = <<<EOT
$person->first_name, привет! Меня зовут Даша, я - HR-менеджер Отдела заботы о сотрудниках. Я помогу тебе с адаптацией в компании и смогу ответить на большинство организационных вопросов

Как новичку, тебе поставлена задача «Адаптация нового сотрудника». Её ты найдешь в Битриксе во вкладке “Задачи и Проекты” либо нажми на колокольчик в правом верхнем углу - там будут все уведомления от постановщиков задач.

Важно! Срок на выполнение – 7 дней, иначе задача будет просрочена. Ты можешь проходить ее параллельно с другими рабочими задачами, главное успеть в недельный срок. Если не успеваешь - напиши мне, договоримся о переносе срока.

Доступ к адаптационному курсу отправила тебе на корпоративную почту (https://lms.toolbox.bz/courses/course/139). Если ничего не пришло - проверь папку Спам.

В ближайшую среду, в 15:30, пройдет Welcome-тренинг (продолжительность - 1 час). Тебе его обязательно нужно посетить и запомнить кодовое слово встречи. Она будет стоять в твоем календаре, за 15 минут до начала придет напоминание. Спланируй, пожалуйста, время.

Ссылка на Welcome-тренинг: https://us06web.zoom.us/j/81610721625?pwd=ak1XbmprdFBUQnRZOWszWU91VHdyUT09
Код доступа: 1

Также мы можем назначить встречу 1:1, где я покажу более подробно как пользоваться Битриксом, расскажу, где смотреть структуру и т.д. (при необходимости).

Какие на данном этапе есть вопросы?  :)
EOT;

        $fields = [
            'DIALOG_ID' => $person->bitrixAccount->platform_id,
            'MESSAGE' => $message,
            'SYSTEM' => 'N',
            'URL_PREVIEW' => 'N',
        ];
        $organization = Organization::find($person->org_id);

        CreateHRMessage::dispatch($fields, $organization)->delay(10);
    }
}
