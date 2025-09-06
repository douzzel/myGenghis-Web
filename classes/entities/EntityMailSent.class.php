<?php

class EntityMailSent extends EntityMail
{
    private int $isDraft;
    private ?string $bcc_email;

    public function getType()
    {
        return 'sent';
    }

    public function getDest()
    {
        return $this->getTo_emailText().' '.$this->getBccText();
    }

    public function getDestLink()
    {
        return "<a href='/Administration/Contacts/".UTILS::getFunction('usernameSMTP')."' title='".UTILS::getFunction('usernameSMTP')."'>".UTILS::getFunction('SiteName')."</a>";
    }

    public function getSerializeBCC()
    {
        return $this->bcc_email;
    }

    private function getBccText()
    {
        if (!unserialize($this->bcc_email)) {
            return in_array($this->bcc_email, ["a:0:{}", "b:0;"]) ? '' : $this->bcc_email;
        }
        $bcc = [];
        foreach (unserialize($this->bcc_email) as $key => $value) {
            $bcc[] = $value ?? $key;
        }
        return implode(', ', $bcc);
    }

    public function getToBcc_emailLink()
    {
        if (!unserialize($this->bcc_email)) {
            return in_array($this->bcc_email, ["a:0:{}", "b:0;"]) ? '' : $this->bcc_email;
        }
        $cc = [];
        foreach (unserialize($this->bcc_email) as $key => $value) {
            if (is_int($key)) {
                $cc[] = "<a href='/Administration/Contacts/{$value}' title='{$value}'>{$value}</a>";
            } else {
                $cc[] = $value ? "<a href='/Administration/Contacts/{$key}' title='{$key}'>{$value}</a>" : "<a href='/Administration/Contacts/{$key}' title='{$key}'>{$key}</a>";
            }
        }
        return implode(', ', $cc);
    }
}
