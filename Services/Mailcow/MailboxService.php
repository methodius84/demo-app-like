<?php

namespace App\Services\Mailcow;

use App\DTO\EmailDTO;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\SmsController;
use App\Mail\MailcowCredentials;
use App\Models\{Email, Person};
use App\Services\CredentialsTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class MailboxService
{
    use CredentialsTrait;

    public function __construct()
    {
    }

    /**
     * @param Person $person
     * @return EmailDTO|null
     */
    public function create(Person $person, string $domain = null): EmailDTO|null
    {
        try{
            $password = $this->genPassword();
            $nickname = $this->setNickname($person);

            if ($domain === null){
                $domain = match ($person->org_id){
                    3 => 'names.works',
                    default => 'likebz.ru',
                };
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://mail.likebz.ru/api/v1/add/mailbox');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"active\": \"1\",\n  \"domain\": \"$domain\",\n  \"local_part\": \"$nickname\",\n  \"name\": \"$person->last_name $person->first_name\",\n  \"password\": \"$password\",\n  \"password2\": \"$password\",\n  \"quota\": \"1024\",\n  \"force_pw_update\": \"0\",\n  \"tls_enforce_in\": \"1\",\n  \"tls_enforce_out\": \"1\",\n  \"tags\": [\n    \"$person->org_id\"]\n}");

            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'X-Api-Key: '.config('services.mailcow.token');
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            $result = json_decode($result);

            if (is_array($result)){
                $result = $result[0];
                if ($result->type === 'success'){
                    // TODO убрать логику записи в другое место
                    $person->update([
                        'email' => $result->log[3]->local_part.'@'.$result->log[3]->domain,
                    ]);
                    Log::channel('telegram')->info('Mailbox created for person '.$person->id, [
                        $result->msg,
                        'password: '.$password,
                    ]);

                    $person->refresh();
                    if ($person->personal_email !== null){
                        \Mail::to($person->personal_email)->send(new MailcowCredentials([
                            'email' => $person->email,
                            'password' => $password,
                        ]));
                    }
                    if ($person->phone !== null){
                        (new SmsController())->sendMessage($person->email, $password, $person->phone);
                    }
                    return $this->createDTO($result, $person);
                }
                else{
                    Log::channel('telegram')->error('Error creating mailbox! '.$result->type, $result->msg);
                    return null;
                }
            }
            else return null;
        }
        catch (\Exception $exception){
            Log::channel('telegram')->error('Error creating mailbox! '.$exception->getMessage());
            return null;
        }
    }

    private function createDTO($result, Person $person = null) : EmailDTO
    {
        return new EmailDTO([
            'username' => $result->log[3]->local_part.'@'.$result->log[3]->domain,
            'active' => true,
            'domain' => $result->log[3]->domain,
            'name' => $result->log[3]->name,
            'quota' => $result->log[3]->quota,
            'local_part' => $result->log[3]->local_part,
            'person_id' => $person->id
        ]);
    }

}
