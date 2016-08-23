<?php

namespace Blekan\Models;

use Illuminate\Database\Eloquent\Model;

class General extends Model
{
    protected $table = 'general';

    protected $fillable = [
      'nav_color',
      'header_color',
      'footer_color',
      'parallax_img_1',
      'parallax_img_2',
      'parallax_img_3',
      'address',
      'telephone',
      'email',
      'email_contact',
      'message_home',
      'message_home_ribbon',
      'message_menu',
      'message_takeaway',
      'logo_image',
      'main_title',
    ];

    public function setContactDetails($address, $telephone, $email, $email_contact)
    {
        $this->update(['address' => $address, 'telephone' => $telephone, 'email' => $email, 'email_contact' => $email_contact]);
    }

    public function setMainColors($colorNav, $colorHeader, $colorFooter)
    {
        $this->update(['nav_color' => $colorNav, 'header_color' => $colorHeader, 'footer_color' => $colorFooter]);
    }

    public function setParalaxImagesAndTitleLogo($uri1, $uri2, $uri3, $title, $logo)
    {
        $this->update(['parallax_img_1' => $uri1, 'parallax_img_2' => $uri2, 'parallax_img_3' => $uri3, 'logo_image' => $logo, 'main_title' => $title]);
    }

    public function setHomeMessages($ribbonMessage, $mainMessage)
    {
        $this->update(['message_home_ribbon' => $ribbonMessage, 'message_home' => $mainMessage]);
    }

    public function setMenuTakeawayMessage($menuMessage, $takeawayMessage)
    {
        $this->update(['message_menu' => $menuMessage, 'message_takeaway' => $takeawayMessage]);
    }
}
