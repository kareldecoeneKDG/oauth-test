<?php

namespace App\Http\Controllers\oAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//use Laravel\Socialite\Facades\Socialite;
use Socialite;

use Illuminate\Support\Facades\Auth;
//use Laravel\Socialite\Facades\Socialite;

use App\Models\User;

class oAuthController extends Controller
{
    //define driver for oAuth, in this case -> microsoft
    private function socialiteProvider($provider)
    {
        //define redirect URL/Route for Socialite
        $redirectUrl = route('oauth.callback', ['provider' => $provider]);
        return Socialite::driver($provider)->redirectUrl($redirectUrl);
    }

    //function to redirect to provider through Socialite
    public function redirectToProvider($provider)
    {
        return $this->socialiteProvider($provider)->redirect();
    }

    //define user after provider callback
    public function handleProviderCallback($provider) {
        $providerUser = $this->socialiteProvider($provider)->user();

        $user = User::firstOrNew(['email' => $providerUser->getEmail()]);

        $user->name = $providerUser->getName();
        $user->firstname = $providerUser->user['givenName'];
        $user->lastname = $providerUser->user['surname'];
        $user->provider_id = $providerUser->getId();
        //$user->jobtitle = $providerUser->user['jobTitle'];
        //$user->location = $providerUser->user['officeLocation'];

        $user->save();

        //make user logged in
        \Auth::login($user, true);
        //after login, go to the right page
        return redirect()->intended('/');
    }
}
