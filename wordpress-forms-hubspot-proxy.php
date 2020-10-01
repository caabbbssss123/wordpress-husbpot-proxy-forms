<?php

/**
 * Plugin Name: Contact Form 7 HubSpot Proxy
 * Plugin URI: https://cyzerg.com/
 * Description: This plugin integrates Wordpress Contact Form 7 forms with HubSpot API
 * Version: 1.0
 * Author: Jeremiah Cabigting
 * Author URI: https://cyzerg.com/
 */

class CF7HubSpotProxy
{

  const HS_PORTAL = '8180457';

  const CONTACT_US_CF7_ID = '5';
  const CONTACT_US_HS_ID = '7ec065ea-ae2d-4ec8-965f-90c7969e4ef5';

  const CARRIERS_CF7_ID = '2219';
  // const CARRIERS_HS_ID = 'e540636e-9ddf-4cee-8aee-ee8eb74d1061'; // OLD
  const CARRIERS_HS_ID = '31e8faf8-564a-41c9-b67b-6b2aef8ace42'; // NEW

  const SHIPPERS_CF7_ID = '2554';
  const SHIPPERS_HS_ID = '0779d223-fd31-4a81-acbe-2be003cd2011';

  // Services
  const FTL_CF7_ID = '2552';
  const FTL_HS_ID = '794cdeac-6a91-480f-ba3a-e33c9d5dfaa9';

  const INTERMODAL_CF7_ID = '2216';
  const INTERMODAL_HS_ID = 'e5bc804c-bab0-4393-990a-d53ae0d7679a';

  const LTL_CF7_ID = '2213';
  const LTL_HS_ID = 'bd8ee72a-d0df-4284-9703-36e5c058d0a3';
  
  const LTLC_CF7_ID = '2551';
  const LTLC_HS_ID = '91e28948-c2aa-4f48-a34d-9cb2893611da';

  const AIR_CF7_ID = '2550';
  const AIR_HS_ID = '16f71c1c-2626-4e6f-a197-8a2b66e3ca7e';

  const CROSS_BORDER_CF7_ID = '2549';
  const CROSS_BORDER_HS_ID = 'cecb5ca4-1734-4c72-9e97-80e74792bcff';

  const PROJECT_CF7_ID = '2548';
  const PROJECT_HS_ID = 'ee33889a-d0aa-4cbf-bc8e-da1a8e6c5fbb';
  
  // Industries
  const FOOD_CF7_ID = '2540';
  const FOOD_HS_ID = 'ff8ec813-b85c-4ffd-bdee-46e7260d7aae';

  const MANUFACTURING_CF7_ID = '2542';
  const MANUFACTURING_HS_ID = '0cbdd690-dd33-4f84-b04f-29241da98bed';

  const CONSUMER_CF7_ID = '2541';
  const CONSUMER_HS_ID = 'f2a02154-12ff-4db7-a6e0-abe49824afb7';

  const CHEMICALPLASTIC_CF7_ID = '2543';
  const CHEMICALPLASTIC_HS_ID = 'e020144c-4f78-476b-b6a6-be32294b3e21';

  const BUILDING_CF7_ID = '2544';
  const BUILDING_HS_ID = '7308f11d-0c0e-4a95-a4cf-81bea6ff817d';

  const PRINTED_CF7_ID = '2545';
  const PRINTED_HS_ID = 'b7b56e9d-4f16-49cc-9684-be001455b3e2';

  const WEARING_APPAREL_CF7_ID = '2546';
  const WEARING_APPAREL_HS_ID = '1eb6e960-38ab-404e-be99-9f36af8f0a19';

  const HEALTH_BEAUTY_CF7_ID = '2547';
  const HEALTH_BEAUTY_HS_ID = '3ed6c3d5-25d9-42d4-b03c-f2f967a47629';

  public function __construct()
  {
    add_action('wpcf7_mail_sent', array(&$this, 'synchronizeHS'), 1, 1);
  }

  public function getHSContext()
  {
    if (isset($_COOKIE['hubspotutk'])) {
      $hubspotutk = $_COOKIE['hubspotutk'];
    } else {
      $hubspotutk = "";
    }

    global $wp;

    $current_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url(add_query_arg(array(), $wp->request));
    $back_id = url_to_postid($current_url);

    $back_title = '';
    if ($back_id > 0) {
      $back_title = get_the_title($back_id);
    }

    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $hs_context = array(
      'hutk' => $hubspotutk,
      'ipAddress' => $ip_addr,
      'pageUrl' => $current_url,
      'pageName' => $back_title
    );

    return json_encode($hs_context);
  }

  public function synchronizeHS($wpcf7_data)
  {
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
      $input = $submission->get_posted_data();

      $form_id = 0;
      $$str_post = '';
      if (self::CONTACT_US_CF7_ID == $input['_wpcf7']) {

        $reason = ($input['reason']) ? $input['reason'] : "";

        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "reason=" . urlencode($reason)
          . "&firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::CONTACT_US_HS_ID;
      }

      if (self::CARRIERS_CF7_ID == $input['_wpcf7']) {

        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::CARRIERS_HS_ID;
      }

      if (self::SHIPPERS_CF7_ID == $input['_wpcf7']) {

        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::SHIPPERS_HS_ID;
      }

      if(self::FTL_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::FTL_HS_ID;
      }

      if(self::INTERMODAL_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::INTERMODAL_HS_ID;
      }

      if(self::LTL_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::LTL_HS_ID;
      }

      if(self::LTLC_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::LTLC_HS_ID;
      }

      if(self::AIR_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::AIR_HS_ID;
      }

      if(self::CROSS_BORDER_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::CROSS_BORDER_HS_ID;
      }

      if(self::PROJECT_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::PROJECT_HS_ID;
      }

      if(self::FOOD_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::FOOD_HS_ID;
      }

      if(self::MANUFACTURING_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::MANUFACTURING_HS_ID;
      }

      if(self::CONSUMER_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::CONSUMER_HS_ID;
      }

      if(self::CHEMICALPLASTIC_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::CHEMICALPLASTIC_HS_ID;
      }

      if(self::BUILDING_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::BUILDING_HS_ID;
      }

      if(self::PRINTED_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::PRINTED_HS_ID;
      }

      if(self::WEARING_APPAREL_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::WEARING_APPAREL_HS_ID;
      }

      if(self::HEALTH_BEAUTY_CF7_ID == $input['_wpcf7']) {
        $firstname = ($input['your-name']) ? $input['your-name'] : "";

        $email = ($input['your-email']) ? $input['your-email'] : "";

        $phone = ($input['phone']) ? $input['phone'] : "";

        $message = ($input['your-message']) ? $input['your-message'] : "";

        $hs_context_json = $this->getHSContext();

        $str_post = "firstname=" . urlencode($firstname)
          . "&email=" . urlencode($email)
          . "&phone=" . urlencode($phone)
          . "&message=" . urlencode($message)
          . "&hs_context=" . urlencode($hs_context_json);

        $form_id = self::HEALTH_BEAUTY_HS_ID;
      }

      if ($form_id) {
        $endpoint = 'https://forms.hubspot.com/uploads/form/v2/' . self::HS_PORTAL . '/' . $form_id;
        $this->postDataToHS($str_post, $endpoint);
      }
    }
    // $wpcf7_data->skip_mail = true;
  }

  public function postDataToHS($data, $endpoint)
  {
    $ch = @curl_init();
    @curl_setopt($ch, CURLOPT_POST, true);
    @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    @curl_setopt($ch, CURLOPT_URL, $endpoint);
    @curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/x-www-form-urlencoded'
    ));
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    @curl_close($ch);
  }
}

$cf7HubSpotProxy = new CF7HubSpotProxy();
