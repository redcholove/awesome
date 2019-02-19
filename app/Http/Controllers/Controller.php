<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function usernamePreg($username) {
        $username_preg = "^[a-zA-Z]\w{6,20}$^";

        if (!preg_match($username_preg, $username)) {
            $this->rtndata ['status'] = 'error';
            $this->rtndata ['message'] = 'error username syntax';
            return $this->rtndata;
        }
    }

    private function passwordPreg($password) {
        //正則沒寫出來
//        $password_preg = "(?=.*[0-9])(?=.*[a-zA-Z]){6,20}$^&";
//        if (!preg_match($password_preg, $password)) {
//            $this->rtndata ['status'] = 'error';
//            $this->rtndata ['message'] = 'error password syntax';
//            return $this->rtndata;
//        }
    }

    public function Register() {
        $username = trim((Input::has ( 'username' )) ? Input::get ( 'username' ) : "");
        $password = trim((Input::has ( 'password' )) ? Input::get ( 'password' ) : "");
        $name = trim((Input::has ( 'name' )) ? Input::get ( 'name' ) : "");

        if ($this->usernamePreg($username)) return response() -> json ($this->rtndata);
        if ($this->passwordPreg($username)) return response() -> json ($this->rtndata);

        try {
            DB::beginTransaction();
            $data ['username'] = $username;
            $data ['password'] = $password;
            $data ['vName'] = $name;

            DB::table('awesome')->insert($data);
            DB::commit();
            $this->rtndata['status'] = 'success';
            $this->rtndata['message'] = 'success';
        }catch ( \Exception $e ) {
            DB::rollback();
            $this->rtndata['status'] = 'error';
            $this->rtndata['message'] = $e;
        }
        return response()->json($this->rtndata);
    }


    public function Login() {
        $username = trim((Input::has ( 'username' )) ? Input::get ( 'username' ) : "");
        $password = trim((Input::has ( 'password' )) ? Input::get ( 'password' ) : "");
        $query['username'] = $username;
        $query['password'] = $password;

        try {
            $user = DB::table('awesome')->where($query)->first();

            if (!$user) {
                $this->rtndata['status'] = 'error';
                $this->rtndata['message'] = 'user data error';
            } else {
                $this->rtndata['status'] = 'success';
                $this->rtndata['message'] = 'login success';
            }

        }catch ( \Exception $e ) {
            $this->rtndata['status'] = 'error';
            $this->rtndata['message'] = $e;
        }
        return response()->json($this->rtndata);
    }
}
