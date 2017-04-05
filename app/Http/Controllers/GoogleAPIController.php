<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Google, Redirect;

class GoogleAPIController extends Controller
{


  public function getIndex(Request $request)
  {
      $code = $request->get('code');
      // get google service
      $client = Google::getClient();

      // check if code is valid

      // if code is provided get user data and sign in
      if ( ! is_null($code))
      {
          // This was a callback request from google, get the token
          $token = $client->fetchAccessTokenWithAuthCode($code);

          // Send a request with it


          $client->setAccessToken($token);

          if ($client->isAccessTokenExpired())
          {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
          }

          $service = Google::make('gmail');
echo '<pre>';
print_r($service);
          die;
          $user = 'me';
          $results = $service->users_labels->listUsersLabels($user);

          echo '<pre>'; print_r($results); die;
      }
      // if not ask for permission first
      else
      {
          // get googleService authorization
          $url = $client->createAuthUrl();

          // return to google login url
          return redirect((string)$url);
      }
  }



  function listMessages($service, $userId) {
    $pageToken = NULL;
    $messages = array();
    $opt_param = array();
    do {
      try {
        if ($pageToken) {
          $opt_param['pageToken'] = $pageToken;
        }
        $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_param);
        if ($messagesResponse->getMessages()) {
          $messages = array_merge($messages, $messagesResponse->getMessages());
          $pageToken = $messagesResponse->getNextPageToken();
        }
      } catch (Exception $e) {
        print 'An error occurred: ' . $e->getMessage();
      }
    } while ($pageToken);

    foreach ($messages as $message) {
      print 'Message with ID: ' . $message->getId() . '<br/>';
    }

    return $messages;
  }

}
