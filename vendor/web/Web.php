<?php
namespace app\vendor\web;
//web操作类
class Web{
    /**
     * 发送post请求
     * @param $url String 请求的URL
     * @param $array array 发送的参数数组
     * @param array $header 头部信息
     * @return mixed
     */
    public static function post($url, $array, $header = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0; 1503-M02 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036558 Safari/537.36 MicroMessenger/6.3.25.861 NetType/WIFI Language/zh_CN");
        //设置提交的url
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
        //设置post数据
        $post_data = $array;
        @curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //获得数据并返回
        return $data;
    }

    /**
     * 发送get请求
     * @param $url String url地址
     * @param array $header 头部其他设置数组
     * @return bool|string
     */
    public static function get($url, $header = [])
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
        //参数为0表示不带头文件，为1表示带头文件
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        //$err=curl_errno($ch);
        // var_dump($output);
        //var_dump(curl_errno($ch),curl_error($ch));
        curl_close($ch);
        return $output;
    }

    /**
     * 发送put请求
     * @param $url String URL
     * @param $data array 数据
     * @param array $header 头部设置
     * @return mixed
     */
    public static function put($url,$data,$header=[]){
        $data = json_encode($data);
        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT"); //设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }
}
