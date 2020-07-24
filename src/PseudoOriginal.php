<?php
namespace zhuweneTrans;
/**
 * 伪原创生成
 */
class PseudoOriginal
{
    /**
     * 伪原创内容
     * @var string
     */
    protected $source = "";
    /**
     * 关键词 多个逗号拼接
     * @var array|string
     */
    protected $keyword = "";


    function __construct($source, $keyword = '')
    {
        $this->source  = strip_tags($source, '<p>');
        $this->keyword = explode(',', $keyword);

    }

    public function content()
    {
        if (!$this->source) return false;

        $text = str_replace(array("\r\n", "\n", "\r"), '[h]', $this->source);
        $text = $this->keywordLock($text);
        $text = $this->keywordUnlock($this->wyc($text));

        return str_replace('[h]', PHP_EOL, $text);
    }

    /**
     * 关键词锁定
     * @param string $content 内容
     * @return mixed|string
     */
    public function keywordLock($content = '')
    {

        foreach ($this->keyword as $id => $age) {
            $content =& $content;
            $content = str_replace($age, "[k$id]", $content);
        }
        return $content;
    }

    /**
     * 关键词解锁
     * @param string $content 内容
     * @return mixed|string
     */
    public function keywordUnlock($content = '')
    {

        foreach ($this->keyword as $id => $age) {
            $content =& $content;
            $content = str_ireplace("[k$id]", $age, $content);
        }
        return $content;

    }

    public function mbStrSplit($string, $len = 1)
    {
        $start  = 0;
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf8");
            $string  = mb_substr($string, $len, $strlen, "utf8");
            $strlen  = mb_strlen($string);
        }
        return $array;
    }

    public function wyc($info)
    {

        $infocount = mb_strlen($info, 'UTF-8');
        //1000以内可用
//        if ($infocount <= 990) {
//            $zh_en = $this->translate($info, 'zh-CN', 'EN');
//            $wyc   = $this->translate($zh_en, 'EN', 'zh-CN');
//        } else {
//            $wyc = "超过字数限制，QQ群：200653131";//92行设置的字数
//        }

        //超过一千可用

        $wyc = '';
        if ($infocount <= 1000) {
            //如果小于或等于1000直接翻译
            $zh_en = $this->translate($info, 'zh-CN', 'EN');
            $wyc   = $this->translate($zh_en, 'EN', 'zh-CN');
        } else {
            //如果大于于1000，每1000字符进行分割循环翻译
            $info = $this->mbStrSplit($info, 800);
            $arr  = count($info);

            for ($i = 0; $i < $arr; $i++) {

                $zh_en = $this->translate($info[$i], 'zh-CN', 'EN');
                $wyc   .= $this->translate($zh_en, 'EN', 'zh-CN');

            }
        }
        return $wyc;
    }

    public function translate($text, $from, $to)
    {
        $url = "https://translate.google.cn/translate_a/single?client=gtx&dt=t&ie=UTF-8&oe=UTF-8&sl=$from&tl=$to&q=" . urlencode($text);
        set_time_limit(0);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.81 Safari/537.36 SE 2.X MetaSr 1.0");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);
        if (!empty($result)) {
            foreach ($result[0] as $k) {
                $v[] = $k[0];
            }
            return implode(" ", $v);
        }
    }

}
