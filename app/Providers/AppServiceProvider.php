<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;



use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
         
        /* Check if the Client's IP is Registered in Database or not */
        $client = new Client();
        /*$response = $client->request('POST', 'http://35.161.41.228/amazonec2/public/check-ip',[
                'form_params' => [
                    'ipaddress' => \Request::ip()
                ]
            ]);
        $response = json_decode($response->getBody(), true);
        */

        $response['status'] = 1;         
        
        $viewShare['local_ip'] = \Request::ip();
        $viewShare['ip_is_allowed'] = $response['status'];
        $viewShare['ip_message'] = ($response['status'] == 0) ? base64_decode('U29ycnkgISBZb3VyIElQIEFkZHJlc3MgaXMgTm90IFJlZ2lzdGVyZWQgaW4gb3V0IFN5c3RlbS4gUGxlYXNlIGNvbnRhY3QgeW91ciBTeXN0ZW0gQWRtaW4=
') : '';

        View::share('view_share' , $viewShare);

        if(isset($response['status']) && $response['status'] ==0){
            echo '<center><div class="alert alert-danger">'.$viewShare['ip_message'].'</div></center>';
            Auth::logout();
            Session::flush();
            return redirect('/');

            exit(0);
            return false;
        }else{
            return true;
        }

        // $demoPeriod = '2016-12-15';
        // if(isset($demoPeriod) && date('Y-m-d') >= $demoPeriod){
        //     /*Auth::logout();
        //     Session::flush();
        //     return redirect('/');*/
        // }        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
