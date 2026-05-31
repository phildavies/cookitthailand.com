<?php
/**
 * Joomla! POWR Icon
*
* @version 1.4
* @author POWR.io
* @package POWR
* @subpackage powr
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgButtonPowrIcon extends JPlugin{
   public function onDisplay($name){
      $jsCode = "
        function uniqueLabel(){
                    return 'xxxxxxxx_'.replace(/[x]/g, function(c) {
                        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
                        return v.toString(16);
                    }) + new Date().getTime();
        }
        jQuery(document).on('click','.powrBtn',function(el){
          //jQuery(document).append(powrModal);
          console.log('POWR Button Clicked');
          if( jQuery(el.target).hasClass('powrBtn') || jQuery(el.target).hasClass('powrIconText') ){
                jQuery('#powrPluginsWrapper').fadeIn();
          }
          return false;
        });
        jQuery(document).on('click','.powrPlugin',function(){
          console.log('POWR Plugin');
          jQuery('#powrPluginsWrapper').fadeIn(); //Show modal
          var slug = jQuery(this).data('slug'); //Slug for app
          var shortcode = '[powr-'+slug+' id=\"'+uniqueLabel()+'\"]';
          jInsertEditorText(shortcode,editor);
          jQuery('#powrPluginsWrapper').fadeOut();
        });
        jQuery(document).on('click','.powrClose',function(){
          console.log('POWR Close');
          jQuery('#powrPluginsWrapper').fadeOut();
        });
        button_html = \"" . str_replace("\n","","
            <span class='powrIconText'>POWR Plugin</span>
            <style>
            @-webkit-keyframes ani_glow {
            0%{ text-shadow:0 0 10px #f3e8b6; }
            50%{ text-shadow:0 0 5px #cc4f4d;}
            100%{ text-shadow:0 0 10px #f3e8b6;}
            }
            @keyframes ani_glow {
            0%{ text-shadow:0 0 10px #f3e8b6; }
            50%{ text-shadow:0 0 5px #cc4f4d; }
            100%{ text-shadow:0 0 10px #f3e8b6; }
            }
            .powrIconText{
            -webkit-animation: ani_glow 2s linear infinite;
            animation: ani_glow 2s linear infinite;
            }
                .icon-powr{
                  background-image: url(//www.powrcdn.com/powr_logos/16x16.gif);
            width: 16px;
            height: 16px;
            position: relative;
            background-size: contain;
            background-repeat: no-repeat;
            vertical-align: top;
            }
            #powrPluginsWrapper {
            position: fixed;
            top: 15%;
            left: 25%;
            width: 50%;
            background: white;
            z-index: 99999999;
            margin: 0 auto;
            min-height: 150px;
            max-height: 60%;
            padding: 15px;
            box-shadow: 4px 5px 20px gray;
            cursor: default;
            overflow: auto;
            -webkit-animation: none;
            animation: none;

            }
            #powrPluginsWrapper .powrHeader{
            font-size: 20px;
            margin-bottom: 10px;
            }
            #powrPluginsWrapper .powrClose{
            position:absolute;
            top: 8px;
            right: 10px;
            color: gray;
            cursor:pointer;
            }
            #powrPluginsWrapper	.powrPlugin{
            float: left;
            width: 128px;
            padding 10px;
            margin: 10px 10px 5px 10px;
            border-radius: 3px;
            text-align:center;
            cursor:pointer;
            position:relative;
            }
            #powrPluginsWrapper .category {
            float: left;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin-top: 10px;
            }
            #powrPluginsWrapper	.powrPluginImage{
            width: 100%
            box-shadow: 3px 3px 10px #c0c0c0;
            margin-bottom: 5px;
            }
            #powrPluginsWrapper	.powrPluginTitle{
            font-size: 12px;
            font-weight: bold;
            display: block;
            height: 20px;
            }
            #powrPluginsWrapper	.powrPluginDescription{
            position: absolute;
            top: 0;
            left: 0;
            font-size: 11px;
            font-weight: 200;
            line-height: 1.2;
            opacity: 0;
            -webkit-transition: all 0.6s;
            transition: all 0.6s;
            background: rgba(255, 255, 255, 0.85);
            padding: 5px 2px;
            height: 100%;
            width: 100%;
            white-space: normal;
            }
            #powrPluginsWrapper	.powrPluginDescription:hover{
            opacity: 1;
            }
            </style>
            <div id='powrPluginsWrapper' style='display:none;'>
                <div class='powrHeader'>Select POWR Plugin</div>
                <span class='powrClose'>X</span>
                <div class='category'>Popular</div><div class='powrPlugin' data-slug='popup'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/popup/128x128.png'>
    <span class='powrPluginTitle'>Popup</span>
    <span class='powrPluginDescription'>Capture contacts and promote offers with custom popups</span>
    </div><div class='powrPlugin' data-slug='multi-slider'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/multiSlider/128x128.png'>
    <span class='powrPluginTitle'>Multi Slider</span>
    <span class='powrPluginDescription'>Engage visitors with a slideshow of images, videos, and banners</span>
    </div><div class='powrPlugin' data-slug='stripe-button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/stripeButton/128x128.png'>
    <span class='powrPluginTitle'>Stripe Button</span>
    <span class='powrPluginDescription'>Get paid instantly with a custom Stripe button</span>
    </div><div class='powrPlugin' data-slug='media-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/mediaGallery/128x128.png'>
    <span class='powrPluginTitle'>Media Gallery</span>
    <span class='powrPluginDescription'>Showcase articles, images, videos, and more!</span>
    </div><div class='powrPlugin' data-slug='social-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/socialFeed/128x128.png'>
    <span class='powrPluginTitle'>Social Feed</span>
    <span class='powrPluginDescription'>Get more Likes and Followers with automatic social streaming</span>
    </div><div class='powrPlugin' data-slug='form-builder'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/formBuilder/128x128.png'>
    <span class='powrPluginTitle'>Form Builder</span>
    <span class='powrPluginDescription'>Create any type of form and customize it to match your site.</span>
    </div><div class='powrPlugin' data-slug='paypal-button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/paypalButton/128x128.png'>
    <span class='powrPluginTitle'>PayPal Button</span>
    <span class='powrPluginDescription'>Get paid instantly with a custom PayPal Button</span>
    </div><div class='powrPlugin' data-slug='payment-button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/paymentButton/128x128.png'>
    <span class='powrPluginTitle'>Payment Button</span>
    <span class='powrPluginDescription'>Get paid instantly with a custom payment button</span>
    </div><div class='powrPlugin' data-slug='countdown-timer'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/countdownTimer/128x128.png'>
    <span class='powrPluginTitle'>Countdown Timer</span>
    <span class='powrPluginDescription'>Create a sense of urgency to drive sales or conversions</span>
    </div><div class='powrPlugin' data-slug='faq'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/faq/128x128.png'>
    <span class='powrPluginTitle'>FAQ</span>
    <span class='powrPluginDescription'>Answer questions in advance with custom FAQs</span>
    </div><div class='category'>Forms & Surveys</div><div class='powrPlugin' data-slug='poll'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/poll/128x128.png'>
    <span class='powrPluginTitle'>Poll</span>
    <span class='powrPluginDescription'>Learn more about your users with a custom poll!</span>
    </div><div class='powrPlugin' data-slug='survey'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/survey/128x128.png'>
    <span class='powrPluginTitle'>Survey</span>
    <span class='powrPluginDescription'>Collect honest feedback with custom surveys</span>
    </div><div class='powrPlugin' data-slug='mailing-list'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/mailingList/128x128.png'>
    <span class='powrPluginTitle'>Mailing List</span>
    <span class='powrPluginDescription'>Grow your mailing list with zero effort</span>
    </div><div class='powrPlugin' data-slug='contact-form'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/contactForm/128x128.png'>
    <span class='powrPluginTitle'>Contact Form</span>
    <span class='powrPluginDescription'>Capture contact info with a simple form</span>
    </div><div class='powrPlugin' data-slug='form-builder'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/formBuilder/128x128.png'>
    <span class='powrPluginTitle'>Form Builder</span>
    <span class='powrPluginDescription'>Create any type of form and customize it to match your site.</span>
    </div><div class='powrPlugin' data-slug='order-form'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/orderForm/128x128.png'>
    <span class='powrPluginTitle'>Order Form</span>
    <span class='powrPluginDescription'>Sell anything on any site.</span>
    </div><div class='category'>Galleries and Sliders</div><div class='powrPlugin' data-slug='multi-slider'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/multiSlider/128x128.png'>
    <span class='powrPluginTitle'>Multi Slider</span>
    <span class='powrPluginDescription'>Engage visitors with a slideshow of images, videos, and banners</span>
    </div><div class='powrPlugin' data-slug='banner-slider'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/bannerSlider/128x128.png'>
    <span class='powrPluginTitle'>Banner Slider</span>
    <span class='powrPluginDescription'>Beautiful sliding banners for images and video.</span>
    </div><div class='powrPlugin' data-slug='event-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/eventGallery/128x128.png'>
    <span class='powrPluginTitle'>Event Gallery</span>
    <span class='powrPluginDescription'>Showcase upcoming events in a beautiful Pinterest-style gallery!</span>
    </div><div class='powrPlugin' data-slug='microblog'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/microblog/128x128.png'>
    <span class='powrPluginTitle'>Microblog</span>
    <span class='powrPluginDescription'>Showcase articles, images, videos, and more!</span>
    </div><div class='powrPlugin' data-slug='photo-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/photoGallery/128x128.png'>
    <span class='powrPluginTitle'>Photo Gallery</span>
    <span class='powrPluginDescription'>A Pinterest-style gallery for unlimited images!</span>
    </div><div class='powrPlugin' data-slug='video-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/videoGallery/128x128.png'>
    <span class='powrPluginTitle'>Video Gallery</span>
    <span class='powrPluginDescription'>Showcase YouTube and Vimeo videos in a beautiful gallery!</span>
    </div><div class='powrPlugin' data-slug='media-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/mediaGallery/128x128.png'>
    <span class='powrPluginTitle'>Media Gallery</span>
    <span class='powrPluginDescription'>Showcase articles, images, videos, and more!</span>
    </div><div class='powrPlugin' data-slug='youtube-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/youtubeGallery/128x128.png'>
    <span class='powrPluginTitle'>YouTube Gallery</span>
    <span class='powrPluginDescription'>Grow your audience with an embeddable YouTube gallery</span>
    </div><div class='powrPlugin' data-slug='video-slider'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/videoSlider/128x128.png'>
    <span class='powrPluginTitle'>Video Slider</span>
    <span class='powrPluginDescription'>A simple slideshow for YouTube and Vimeo videos!</span>
    </div><div class='powrPlugin' data-slug='image-slider'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/imageSlider/128x128.png'>
    <span class='powrPluginTitle'>Image Slider</span>
    <span class='powrPluginDescription'>A beautiful, responsive slider for images and photos!</span>
    </div><div class='powrPlugin' data-slug='flickr-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/flickrGallery/128x128.png'>
    <span class='powrPluginTitle'>Flickr Gallery</span>
    <span class='powrPluginDescription'>Get more Flickr followers with a custom gallery</span>
    </div><div class='powrPlugin' data-slug='vimeo-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/vimeoGallery/128x128.png'>
    <span class='powrPluginTitle'>Vimeo Gallery</span>
    <span class='powrPluginDescription'>Get more views on Vimeo with embeddable playlists</span>
    </div><div class='powrPlugin' data-slug='event-slider'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/eventSlider/128x128.png'>
    <span class='powrPluginTitle'>Event Slider</span>
    <span class='powrPluginDescription'>Showcase upcoming events with images and videos in this responsive slider!</span>
    </div><div class='category'>Social Media</div><div class='powrPlugin' data-slug='tumblr-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/tumblrFeed/128x128.png'>
    <span class='powrPluginTitle'>Tumblr Feed</span>
    <span class='powrPluginDescription'>Display multiple tumblr accounts in a single feed!</span>
    </div><div class='powrPlugin' data-slug='youtube-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/youtubeGallery/128x128.png'>
    <span class='powrPluginTitle'>YouTube Gallery</span>
    <span class='powrPluginDescription'>Grow your audience with an embeddable YouTube gallery</span>
    </div><div class='powrPlugin' data-slug='twitter-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/twitterFeed/128x128.png'>
    <span class='powrPluginTitle'>Twitter Feed</span>
    <span class='powrPluginDescription'>Get more followers, favorites, and retweets on Twitter</span>
    </div><div class='powrPlugin' data-slug='social-media-icons'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/socialMediaIcons/128x128.png'>
    <span class='powrPluginTitle'>Social Media Icons</span>
    <span class='powrPluginDescription'>Grow your social media following with linked icons</span>
    </div><div class='powrPlugin' data-slug='rss-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/rssFeed/128x128.png'>
    <span class='powrPluginTitle'>RSS Feed</span>
    <span class='powrPluginDescription'>Combine and display content from multiple RSS feeds!</span>
    </div><div class='powrPlugin' data-slug='flickr-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/flickrGallery/128x128.png'>
    <span class='powrPluginTitle'>Flickr Gallery</span>
    <span class='powrPluginDescription'>Get more Flickr followers with a custom gallery</span>
    </div><div class='powrPlugin' data-slug='pinterest-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/pinterestFeed/128x128.png'>
    <span class='powrPluginTitle'>Pinterest Feed</span>
    <span class='powrPluginDescription'>Get more pins with an automatic Pinterest feed</span>
    </div><div class='powrPlugin' data-slug='social-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/socialFeed/128x128.png'>
    <span class='powrPluginTitle'>Social Feed</span>
    <span class='powrPluginDescription'>Get more Likes and Followers with automatic social streaming</span>
    </div><div class='powrPlugin' data-slug='comments'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/comments/128x128.png'>
    <span class='powrPluginTitle'>Comments</span>
    <span class='powrPluginDescription'>Build a community of visitors or shoppers</span>
    </div><div class='powrPlugin' data-slug='instagram-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/instagramFeed/128x128.png'>
    <span class='powrPluginTitle'>Instagram Feed</span>
    <span class='powrPluginDescription'>Get more Instagram followers with an embedded gallery</span>
    </div><div class='powrPlugin' data-slug='reviews'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/reviews/128x128.png'>
    <span class='powrPluginTitle'>Reviews</span>
    <span class='powrPluginDescription'>Collect and display social proof with visitor reviews</span>
    </div><div class='powrPlugin' data-slug='vimeo-gallery'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/vimeoGallery/128x128.png'>
    <span class='powrPluginTitle'>Vimeo Gallery</span>
    <span class='powrPluginDescription'>Get more views on Vimeo with embeddable playlists</span>
    </div><div class='powrPlugin' data-slug='facebook-feed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/facebookFeed/128x128.png'>
    <span class='powrPluginTitle'>Facebook Feed</span>
    <span class='powrPluginDescription'>Get more Likes on Facebook with an automatic FB feed</span>
    </div><div class='category'>eCommerce</div><div class='powrPlugin' data-slug='stripe-button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/stripeButton/128x128.png'>
    <span class='powrPluginTitle'>Stripe Button</span>
    <span class='powrPluginDescription'>Get paid instantly with a custom Stripe button</span>
    </div><div class='powrPlugin' data-slug='digital-download'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/digitalDownload/128x128.png'>
    <span class='powrPluginTitle'>Digital Download</span>
    <span class='powrPluginDescription'>Sell music, videos, ebooks, and more!</span>
    </div><div class='powrPlugin' data-slug='price-table'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/priceTable/128x128.png'>
    <span class='powrPluginTitle'>Price Table</span>
    <span class='powrPluginDescription'>Sell more with side-by-side pricing tables</span>
    </div><div class='powrPlugin' data-slug='plan-comparison'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/planComparison/128x128.png'>
    <span class='powrPluginTitle'>Plan Comparison</span>
    <span class='powrPluginDescription'>Display and sell products, plans, and services in a side-by-side comparison! </span>
    </div><div class='powrPlugin' data-slug='ecommerce'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/ecommerce/128x128.png'>
    <span class='powrPluginTitle'>Ecommerce</span>
    <span class='powrPluginDescription'>Sell more products, downloads, or subscriptions instantly</span>
    </div><div class='powrPlugin' data-slug='paypal-button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/paypalButton/128x128.png'>
    <span class='powrPluginTitle'>PayPal Button</span>
    <span class='powrPluginDescription'>Get paid instantly with a custom PayPal Button</span>
    </div><div class='powrPlugin' data-slug='order-form'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/orderForm/128x128.png'>
    <span class='powrPluginTitle'>Order Form</span>
    <span class='powrPluginDescription'>Sell anything on any site.</span>
    </div><div class='powrPlugin' data-slug='payment-button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/paymentButton/128x128.png'>
    <span class='powrPluginTitle'>Payment Button</span>
    <span class='powrPluginDescription'>Get paid instantly with a custom payment button</span>
    </div><div class='category'>POWR Tools</div><div class='powrPlugin' data-slug='popup'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/popup/128x128.png'>
    <span class='powrPluginTitle'>Popup</span>
    <span class='powrPluginDescription'>Capture contacts and promote offers with custom popups</span>
    </div><div class='powrPlugin' data-slug='hit-counter'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/hitCounter/128x128.png'>
    <span class='powrPluginTitle'>Hit Counter</span>
    <span class='powrPluginDescription'>Track and display visits for any page!</span>
    </div><div class='powrPlugin' data-slug='graph'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/graph/128x128.png'>
    <span class='powrPluginTitle'>Graph</span>
    <span class='powrPluginDescription'>Graphs and charts for any website!</span>
    </div><div class='powrPlugin' data-slug='job-board'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/jobBoard/128x128.png'>
    <span class='powrPluginTitle'>Job Board</span>
    <span class='powrPluginDescription'>Post Jobs and hire fast</span>
    </div><div class='powrPlugin' data-slug='resume'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/resume/128x128.png'>
    <span class='powrPluginTitle'>Resume</span>
    <span class='powrPluginDescription'>Create, customize, and display your resume or CV!</span>
    </div><div class='powrPlugin' data-slug='notification-bar'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/notificationBar/128x128.png'>
    <span class='powrPluginTitle'>Notification Bar</span>
    <span class='powrPluginDescription'>Engage and direct your visitors with an effective & customizable Notification Bar.</span>
    </div><div class='powrPlugin' data-slug='holiday-countdown'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/holidayCountdown/128x128.png'>
    <span class='powrPluginTitle'>Holiday Countdown</span>
    <span class='powrPluginDescription'>Count down to any holiday, birthday, or special event!</span>
    </div><div class='powrPlugin' data-slug='menu'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/menu/128x128.png'>
    <span class='powrPluginTitle'>Menu</span>
    <span class='powrPluginDescription'>Create a stunning restaurant menu in minutes</span>
    </div><div class='powrPlugin' data-slug='tabs'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/tabs/128x128.png'>
    <span class='powrPluginTitle'>Tabs</span>
    <span class='powrPluginDescription'>Help visitors navigate your site and content</span>
    </div><div class='powrPlugin' data-slug='button'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/button/128x128.png'>
    <span class='powrPluginTitle'>Button</span>
    <span class='powrPluginDescription'>Custom buttons for links, downloads, promotions, or social accounts.</span>
    </div><div class='powrPlugin' data-slug='scroll-to-top'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/scrollToTop/128x128.png'>
    <span class='powrPluginTitle'>Scroll To Top</span>
    <span class='powrPluginDescription'>Easily take your users anywhere on your page with a custom scroll button.</span>
    </div><div class='powrPlugin' data-slug='comments'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/comments/128x128.png'>
    <span class='powrPluginTitle'>Comments</span>
    <span class='powrPluginDescription'>Build a community of visitors or shoppers</span>
    </div><div class='powrPlugin' data-slug='music-player'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/musicPlayer/128x128.png'>
    <span class='powrPluginTitle'>Music Player</span>
    <span class='powrPluginDescription'>Embed audio tracks in any website</span>
    </div><div class='powrPlugin' data-slug='reviews'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/reviews/128x128.png'>
    <span class='powrPluginTitle'>Reviews</span>
    <span class='powrPluginDescription'>Collect and display social proof with visitor reviews</span>
    </div><div class='powrPlugin' data-slug='count-up-timer'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/countUpTimer/128x128.png'>
    <span class='powrPluginTitle'>Count Up Timer</span>
    <span class='powrPluginDescription'>Count up from any time, date, or number.</span>
    </div><div class='powrPlugin' data-slug='weather'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/weather/128x128.png'>
    <span class='powrPluginTitle'>Weather</span>
    <span class='powrPluginDescription'>Track and display a real-time forecast for any location!</span>
    </div><div class='powrPlugin' data-slug='chat'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/chat/128x128.png'>
    <span class='powrPluginTitle'>Chat</span>
    <span class='powrPluginDescription'>Easily provide live customer support with Facebook Messenger from your website.</span>
    </div><div class='powrPlugin' data-slug='file-embed'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/fileEmbed/128x128.png'>
    <span class='powrPluginTitle'>File Embed</span>
    <span class='powrPluginDescription'>Embed PDFs and almost any other files in your website!</span>
    </div><div class='powrPlugin' data-slug='about-us'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/aboutUs/128x128.png'>
    <span class='powrPluginTitle'>About Us</span>
    <span class='powrPluginDescription'>Build visitor trust with a professional profile</span>
    </div><div class='powrPlugin' data-slug='countdown-timer'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/countdownTimer/128x128.png'>
    <span class='powrPluginTitle'>Countdown Timer</span>
    <span class='powrPluginDescription'>Create a sense of urgency to drive sales or conversions</span>
    </div><div class='powrPlugin' data-slug='map'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/map/128x128.png'>
    <span class='powrPluginTitle'>Map</span>
    <span class='powrPluginDescription'>Get more visitors with a custom map</span>
    </div><div class='powrPlugin' data-slug='faq'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/faq/128x128.png'>
    <span class='powrPluginTitle'>FAQ</span>
    <span class='powrPluginDescription'>Answer questions in advance with custom FAQs</span>
    </div><div class='category'>Image Manipulation</div><div class='powrPlugin' data-slug='photo-watermark'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/photoWatermark/128x128.png'>
    <span class='powrPluginTitle'>Photo Watermark</span>
    <span class='powrPluginDescription'>Add a watermark to a single image.</span>
    </div><div class='powrPlugin' data-slug='image-resizer'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/imageResizer/128x128.png'>
    <span class='powrPluginTitle'>Image Resizer</span>
    <span class='powrPluginDescription'>Adjust the size, shape, and filter for a single image.</span>
    </div><div class='powrPlugin' data-slug='photo-filter'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/photoFilter/128x128.png'>
    <span class='powrPluginTitle'>Photo Filter</span>
    <span class='powrPluginDescription'>Photoshop-style filters for any image!</span>
    </div><div class='powrPlugin' data-slug='photo-editor'>
    <img class='powrPluginImage' src='//www.powrcdn.com/plugins/photoEditor/128x128.png'>
    <span class='powrPluginTitle'>Photo Editor</span>
    <span class='powrPluginDescription'>Edit, filter, and resize any image!</span>
    </div>
            </div>")."\";

function loadPowrButton(){
  if( jQuery('.icon-powr').length==0){
    setTimeout(function(){
      loadPowrButton();
    },250)
    return;
  }
  jQuery('.icon-powr').parents('button').toggleClass('btn powrBtn',true);
  if( jQuery('.icon-powr').siblings('span').length > 0){ //Tinymce
    jQuery('.icon-powr').siblings('span').html( jQuery.parseHTML(button_html) );
  }else{
    jQuery('.powrBtn').append( jQuery.parseHTML(button_html) );
  }
}
loadPowrButton();
";
      $doc = JFactory::getDocument();
      $doc->addScriptDeclaration($jsCode);
          $button = new JObject();
      $button->modal = false;
      $button->class = 'btn powrBtn';
      $button->title = 'POWR Button';
      $button->text = 'POWR Plugins';
      $button->name = 'powr'; //Makes an icon-powr class
      $button->link = '#';
      return $button;
    }
}
?>