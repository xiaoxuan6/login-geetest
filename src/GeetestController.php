<?php

namespace James\Geetest;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use James\Geetest\Lib\GeetestLib;
use Illuminate\Http\Request;

class GeetestController extends Controller
{
    public function index()
    {
        return view('geetest::login');
    }

    public function start(){
        $GtSdk = new GeetestLib(config('geetest.CAPTCHA_ID'), config('geetest.PRIVATE_KEY'));
        session_start();

        $data = array(
            "user_id" => "test",
            "client_type" => "web",
            "ip_address" => "127.0.0.1"
        );

        $status = $GtSdk->pre_process($data, 1);
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $data['user_id'];
        return $GtSdk->get_response_str();
    }

    public function verify(Request $request){
        session_start();
        $GtSdk = new GeetestLib(config('geetest.CAPTCHA_ID'), config('geetest.PRIVATE_KEY'));

        $data = array(
            "user_id" => $_SESSION['user_id'],
            "client_type" => "web",
            "ip_address" => "127.0.0.1"
        );

        if ($_SESSION['gtserver'] == 1) {   //服务器正常
            if (!$GtSdk->success_validate($request->geetest_challenge, $request->geetest_validate, $request->geetest_seccode, $data))
                return Redirect::back()->withInput()->withErrors(['captcha' => '验证失败']);
        }else{  //服务器宕机,走failback模式
            if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode']))
                return Redirect::back()->withInput()->withErrors(['captcha' => '验证失败']);
        }

        $credentials = $request->only(['username', 'password']);
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            admin_toastr(trans('admin.login_successful'));

            return redirect()->intended(config('admin.route.prefix'));
        }

        return Redirect::back()->withInput()->withErrors(['username' => $this->getFailedLoginMessage()]);
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? trans('auth.failed')
            : 'These credentials do not match our records.';
    }
}