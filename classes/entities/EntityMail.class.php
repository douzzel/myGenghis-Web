<?php

abstract class EntityMail
{
    protected int $id;
    protected ?string $subject;
    protected ?string $textHtml;
    protected ?string $textPlain;
    protected ?string $cc_email;
    protected ?string $to_email;
    protected ?string $from_email;
    protected ?string $attachments;
    protected ?string $date;
    protected int $MID;
    protected int $isRead;
    protected int $important;
    protected int $deleted;
    protected ?int $flag;

    public function getSubject()
    {
        return self::decode($this->subject);
    }

    public function getSummary()
    {
        $textMail = $this->textPlain ? self::decode($this->textPlain) : strip_tags(self::decode($this->textHtml));
        if (strlen($textMail) > 200) {
            $textMail = mb_substr(strip_tags($textMail), 0, 200, 'UTF-8').'…';
        }

        return $textMail;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getFormatDate()
    {
        if (UTILS::date(null, 'Y-m-d') == UTILS::date($this->date, 'Y-m-d')) {
            return UTILS::date($this->date, 'H:i');
        }
        if (UTILS::date(null, 'Y-m') == UTILS::date($this->date, 'Y-m')) {
            return UTILS::date($this->date, 'D d');
        }
        if (UTILS::date(null, 'Y') == UTILS::date($this->date, 'Y')) {
            return UTILS::date($this->date, 'D d F');
        }

        return UTILS::date($this->date, 'd/m/Y');
    }

    public function getLongFormatDate()
    {
        return UTILS::date($this->date, 'D d F Y à H\hi');
    }

    public function getMID()
    {
        return $this->MID;
    }

    public function getTo_email()
    {
        if (!unserialize($this->to_email)) {
            return $this->to_email;
        }
        $cc = [];
        foreach (unserialize($this->to_email) as $key => $value) {
            $cc[] = $key;
        }
        return implode(', ', $cc);
    }

    public function getTo_emailLink()
    {
        if (!unserialize($this->to_email)) {
            return in_array($this->to_email, ["a:0:{}", "b:0;"]) ? '' : $this->to_email;
        }
        $cc = [];
        foreach (unserialize($this->to_email) as $key => $value) {
            if (is_int($key)) {
                $cc[] = "<a href='/Administration/Contacts/{$value}' title='{$value}'>{$value}</a>";
            } else {
                $cc[] = $value ? "<a href='/Administration/Contacts/{$key}' title='{$key}'>{$value}</a>" : "<a href='/Administration/Contacts/{$key}' title='{$key}'>{$key}</a>";
            }
        }
        return implode(', ', $cc);
    }

    public function getToBcc_emailLink()
    {
        return '';
    }

    public function getCC_emailLink()
    {
        if (!$this->cc_email || !unserialize($this->cc_email)) {
            return in_array($this->cc_email, ["a:0:{}", "b:0;"]) ? '' : $this->cc_email;
        }
        $cc = [];
        foreach (unserialize($this->cc_email) as $key => $value) {
            if (is_int($key)) {
                $cc[] = "<a href='/Administration/Contacts/{$value}' title='{$value}'>{$value}</a>";
            } else {
                $cc[] = $value ? "<a href='/Administration/Contacts/{$key}' title='{$key}'>{$value}</a>" : "<a href='/Administration/Contacts/{$key}' title='{$key}'>{$key}</a>";
            }
        }
        return '<span style="color: #61c7b3">Copie à </span> ' . implode(', ', $cc);
    }

    public function getTo_emailText()
    {
        if (!unserialize($this->to_email)) {
            return in_array($this->to_email, ["a:0:{}", "b:0;"]) ? '' : $this->to_email;
        }
        $cc = [];
        foreach (unserialize($this->to_email) as $key => $value) {
            $cc[] = $value ?? $key;
        }
        return implode(', ', $cc);
    }

    public function getFrom_email()
    {
        return $this->from_email;
    }

    public function getFromLink()
    {
        return $this->from_email;
    }

    public function getSerializeCC()
    {
        return $this->to_email;
    }

    public function getSerializeBCC()
    {
        return '';
    }

    public function getTextHtml()
    {
        if ($this->textHtml) {
            $attachments = $this->getAttachments();
            if ($attachments) {
                $array = [];
                $urlArray = [];
                foreach ($attachments as $attach) {
                    if ($attach['cid']) {
                        $array[] = 'src="cid:'.$attach['cid'].'"';
                        $urlArray[] = 'src="'.$attach['url'].'"';
                    }
                }
                return str_replace($array, $urlArray, self::decode($this->textHtml));
            } else {
                return self::decode($this->textHtml);
            }
        } else {
            return '<pre style="white-space: break-spaces;">'.self::decode($this->textPlain).'</pre>';
        }
    }

    public function getHtmlList()
    {
        return "<li data-id='{$this->getId()}' data-type='{$this->getType()}'
        class='mail ".($this->getIsRead() ? '' : 'unread')."' onclick='loadOneMail(event)'>
        <div class='mail-col mail-col-1'>
            <span class='dot'></span>
            <div class='checkbox-wrapper'>
                <input type='checkbox' id='chk{$this->getId()}'>
                <label for='chk{$this->getId()}' class='toggle'></label>
            </div>
            <p class='title'>{$this->getDest()}</p>
            <span class='star-toggle material-icons' onclick='importantMail(event, {$this->getId()})'>".($this->important ? 'star' : 'star_outline')."</span>
        </div>
        <div class='mail-col mail-col-2'>
            <div class='subject'>{$this->getSubject()}
                &nbsp;&ndash;&nbsp;
                <span class='teaser'>{$this->getSummary()}</span>
            </div>
            <div class='date'>{$this->getFormatDate()}</div>
        </div>
    </li>";
    }

    public function getIsRead()
    {
        return $this->isRead;
    }

    public function getImportant()
    {
        return $this->important;
    }

    abstract protected function getType();

    abstract protected function getDest();

    protected static function decode($content)
    {
        return iconv('utf-8', 'latin1', $content);
    }

    public function getAttachments()
    {
        $attachments = $this->attachments ? unserialize($this->attachments) : null;
        $arr = [];
        if($attachments != NULL){
            foreach($attachments as $value){

                if(isset($value)){
                    $explode = explode('::', $value);
                    if ($explode && isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {
                        $arr[] = [
                            'icon' => MineType::mime_content_type('/uploads'.$explode[1]),
                            'url' => "/uploads/ticket-system/imap/attachments/{$explode[2]}.bin",
                            'name' => $explode[1],
                            'cid' => $explode[0]
                        ];
                    } else if (isset($explode[0]) && !isset($explode[1])) {
                        $arr[] = [
                            'icon' => MineType::mime_content_type("/uploads/attachments/{$value}"),
                            'url' => "/uploads/attachments/{$value}",
                            'name' => $value,
                            'cid' => $value
                        ];
                    }
                }
            }
        }
        return $arr;
    }

    public function getId()
    {
        return $this->id;
    }
}
