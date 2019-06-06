<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use App\UserModel;
class PracticaController extends Controller
{
    public $uid=null;
    public function login()
    {
        return view('user/login');
    }
    public function logindo(Request $request)
    {
        $name=request()->input('name');
        $pwd=request()->input('pwd');
        $info=UserModel::where(['name'=>$name])->first();
        if($info){
            if(password_verify($pwd,$info->pwd)){
                $token=substr(sha1($info->uid.time().str::random(10)),5,15);
                $key='uid_token'.$info->uid;
                Redis::set($key,$token);
                Redis::expire($key,604800);
                //echo '登录成功';
            }else{
//                setcookie('ip',$_SERVER['REMOTE_ADDR'],time()+3600);
//                $num=Redis::incr($_SERVER['REMOTE_ADDR']);
//                echo $num.'<hr>';
                $ip=$_SERVER['REMOTE_ADDR'];
                $log = "\n>>>>>> " .date('Y-m-d H:i:s') . ' '.$ip . " \n";
                is_dir('logs') or mkdir('logs', 0777, true);
                file_put_contents('logs/error',$log,FILE_APPEND);
                die('登录失败');
            }
        }else{
            die('信息不正确');
        }
        $data=[
            'name'=>$name,
            'pwd'=>$pwd,
            'email'=>$info->email,
        ];
        $json=json_encode($data);
        $method='AES-256-CBC';
        $key='bruno';
        $option=OPENSSL_RAW_DATA;
        $iv='0123456789abcdef';
        //加密
        $enc=openssl_encrypt($json,$method,$key,$option,$iv);
        $base64=base64_encode($enc);
        echo $base64.'<hr>';
        $url='http://www.laravel0529.com/show';
        $ch = curl_init();
        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$base64);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);
        // 抓取URL并把它传递给浏览器
        $cu=curl_exec($ch);
        //var_dump($cu);
        $errorcode=curl_error($ch);
        if($errorcode>0){
            die('错误码：'.$errorcode);
        }
        // 关闭cURL资源，并且释放系统资源
        curl_close($ch);
    }
    public function show()
    {
        $data=file_get_contents('php://input');
        echo $data.'<hr>';
        $method='AES-256-CBC';
        $key='bruno';
        $option=OPENSSL_RAW_DATA;
        $iv='0123456789abcdef';
        //解密
        $base64=base64_decode($data);
        $ope=openssl_decrypt($base64,$method,$key,$option,$iv);
        echo $ope;
    }
    public function logout()
    {
        if($this->checkLogout()){
            $key='uid_token'.$this->uid;
            Redis::delete($key);
            echo '成功退出';
        }else{
            echo '非法操作';
        }
    }
    public function checkLogout()
    {
        if(!isset($_SERVER['HTTP_TOKEN'])||!isset($_SERVER['HTTP_UID'])){
            return false;
        }
        $token=$_SERVER['HTTP_TOKEN'];
        $uid=intval($_SERVER['HTTP_UID']);
        $this->uid=$uid;
        $key='uid_token'.$uid;
        $local_token=Redis::get($key);
        if($token!=$local_token){
            return false;
        }
        return true;
    }
    public function cdnImg()
    {
        return view('cdn/img');
    }
}
