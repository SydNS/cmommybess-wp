<!-- header styles -->

<?php
   $localFonts = apply_filters('get_local_fonts', '');
?>
<?php if ($localFonts) : ?> 
   <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/<?php echo $localFonts; ?>" media="screen" type="text/css" />
<?php else : ?>
   <?php endif; ?>

<style>.u-header {
  min-height: 219px;
}
.u-header .u-group-1 {
  margin-top: 0;
  margin-bottom: 0;
  min-height: 34px;
}
.u-header .u-container-layout-1 {
  padding-left: 30px;
  padding-right: 30px;
}
.u-header .u-social-icons-1 {
  white-space: nowrap;
  height: 16px;
  min-height: 16px;
  width: 115px;
  min-width: 115px;
  margin: 0 88px 0 auto;
}
.u-header .u-icon-1 {
  height: 100%;
  color: rgb(128, 128, 128) !important;
}
.u-header .u-icon-2 {
  height: 100%;
  color: rgb(128, 128, 128) !important;
}
.u-header .u-icon-3 {
  height: 100%;
  color: rgb(128, 128, 128) !important;
}
.u-header .u-icon-4 {
  height: 100%;
  color: rgb(128, 128, 128) !important;
}
.u-header .u-image-1 {
  margin: 5px auto 0 calc(((100% - 1140px) / 2) + 53px);
}
.u-header .u-logo-image-1 {
  max-width: 137px;
  max-height: 137px;
}
.u-header .u-search-1 {
  width: 292px;
  min-height: 37px;
  margin: -89px auto 0 709px;
}
.u-header .u-search-icon-1 {
  font-size: 0.75rem;
}
.u-header .u-search-input-1 {
  font-size: 0.875rem;
}
.u-header .u-icon-5 {
  height: 20px;
  width: 20px;
  margin: -29px auto 0 1055px;
}
.u-header .u-icon-6 {
  height: 20px;
  width: 20px;
  margin: -20px auto 0 1121px;
}
.u-header .u-group-2 {
  min-height: 51px;
  margin-bottom: -37px;
  margin-top: 63px;
}
.u-header .u-container-layout-2 {
  padding-left: 30px;
  padding-right: 30px;
}
.u-header .u-menu-1 {
  margin: 9px auto 0;
}
.u-header .u-nav-1 {
  font-size: 1rem;
}
.u-block-0fa8-40 {
  font-size: 1rem;
}
.u-header .u-menu-close-1 {
  height: 28px;
  margin: -74px 21px 0 397px;
}
.u-header .u-nav-2 {
  font-size: 1rem;
}
.u-block-0fa8-41 {
  font-size: 1rem;
}
@media (max-width: 1199px) {
  .u-header {
    min-height: 145px;
  }
  .u-header .u-group-1 {
    margin-top: 1px;
    min-height: 28px;
  }
  .u-header .u-social-icons-1 {
    margin-top: 8px;
    margin-right: 154px;
  }
  .u-header .u-icon-1 {
    color: rgb(59, 89, 152) !important;
  }
  .u-header .u-icon-2 {
    color: rgb(85, 172, 238) !important;
  }
  .u-header .u-icon-3 {
    color: rgb(197, 54, 164) !important;
  }
  .u-header .u-icon-4 {
    color: rgb(210, 34, 21) !important;
  }
  .u-header .u-image-1 {
    width: auto;
    margin-top: 1px;
    margin-left: calc(((100% - 940px) / 2) + 73px);
  }
  .u-header .u-logo-image-1 {
    max-width: 87px;
    max-height: 87px;
  }
  .u-header .u-search-1 {
    width: 207px;
    height: auto;
    min-height: 38px;
    margin-top: -56px;
    margin-right: calc(((100% - 940px) / 2) + 263px);
    margin-left: auto;
  }
  .u-header .u-search-icon-1 {
    font-size: 0.875rem;
  }
  .u-header .u-icon-5 {
    color: rgb(71, 138, 201) !important;
    margin-top: -32px;
    margin-right: calc(((100% - 940px) / 2) + 160px);
    margin-left: auto;
  }
  .u-header .u-icon-6 {
    color: rgb(71, 138, 201) !important;
    margin-right: calc(((100% - 940px) / 2) + 215px);
    margin-left: auto;
  }
  .u-header .u-group-2 {
    margin-top: 30px;
    min-height: 35px;
    margin-bottom: 1px;
  }
  .u-header .u-menu-1 {
    width: auto;
    margin-top: 1px;
    margin-left: 103px;
  }
  .u-header .u-nav-1 {
    font-size: 0.75rem;
  }
}
@media (max-width: 991px) {
  .u-header {
    min-height: 145px;
  }
  .u-header .u-group-1 {
    min-height: 27px;
    margin-top: 0;
  }
  .u-header .u-social-icons-1 {
    margin-top: 8px;
    margin-right: 50px;
  }
  .u-header .u-image-1 {
    margin-top: 5px;
    margin-left: calc(((100% - 720px) / 2));
  }
  .u-header .u-logo-image-1 {
    max-width: 79px;
    max-height: 79px;
  }
  .u-header .u-search-1 {
    width: 238px;
    margin-top: -56px;
    margin-right: calc(((100% - 720px) / 2) + 122px);
  }
  .u-header .u-icon-5 {
    height: 16px;
    width: 16px;
    margin-top: -27px;
    margin-right: calc(((100% - 720px) / 2) + 81px);
  }
  .u-header .u-icon-6 {
    height: 16px;
    width: 16px;
    margin-top: -16px;
    margin-right: calc(((100% - 720px) / 2) + 29px);
  }
  .u-header .u-group-2 {
    margin-top: 26px;
    min-height: 33px;
  }
  .u-header .u-menu-1 {
    margin-top: 9px;
    margin-left: 68px;
  }
  .u-header .u-nav-1 {
    font-size: 0.625rem;
  }
}
@media (max-width: 767px) {
  .u-header {
    min-height: 137px;
  }
  .u-header .u-group-1 {
    min-height: 27px;
  }
  .u-header .u-container-layout-1 {
    padding-left: 10px;
    padding-right: 10px;
  }
  .u-header .u-social-icons-1 {
    margin-right: calc(((100% - 1140px) / 2) + 300px);
  }
  .u-header .u-image-1 {
    height: 69px;
    margin-left: 110px;
  }
  .u-header .u-search-1 {
    width: 226px;
    margin-right: calc(((100% - 540px) / 2) + 122px);
    margin-left: 304px;
  }
  .u-header .u-icon-5 {
    margin-top: -26px;
    margin-right: calc(((100% - 540px) / 2) + -8px);
  }
  .u-header .u-icon-6 {
    margin-top: -19px;
    margin-right: calc(((100% - 540px) / 2) + 29px);
  }
  .u-header .u-group-2 {
    margin-top: 28px;
    margin-bottom: 0;
  }
  .u-header .u-container-layout-2 {
    padding-left: 10px;
    padding-right: 10px;
  }
  .u-header .u-menu-1 {
    width: 84px;
    margin-left: calc(((100% - 1140px) / 2) + 525px);
  }
}
@media (max-width: 575px) {
  .u-header .u-group-1 {
    margin-top: 1px;
  }
  .u-header .u-social-icons-1 {
    margin-top: 6px;
    margin-right: auto;
  }
  .u-header .u-icon-1 {
    color: rgb(128, 128, 128) !important;
  }
  .u-header .u-icon-2 {
    color: rgb(128, 128, 128) !important;
  }
  .u-header .u-icon-3 {
    color: rgb(128, 128, 128) !important;
  }
  .u-header .u-icon-4 {
    color: rgb(128, 128, 128) !important;
  }
  .u-header .u-image-1 {
    margin-left: calc(((100% - 340px) / 2));
  }
  .u-header .u-logo-image-1 {
    max-width: 56px;
    max-height: 56px;
  }
  .u-header .u-search-1 {
    min-height: 30px;
    width: 189px;
    margin-top: -45px;
    margin-right: calc(((100% - 340px) / 2) + 62px);
    margin-left: auto;
  }
  .u-header .u-search-icon-1 {
    font-size: 0.625rem;
  }
  .u-header .u-icon-5 {
    height: 12px;
    width: 12px;
    margin-top: -22px;
    margin-right: calc(((100% - 340px) / 2) + 33px);
  }
  .u-header .u-icon-6 {
    height: 14px;
    width: 15px;
    margin-top: -12px;
    margin-right: calc(((100% - 340px) / 2) + -18px);
  }
  .u-header .u-group-2 {
    margin-top: 23px;
    margin-bottom: -12px;
    min-height: 34px;
  }
  .u-header .u-menu-1 {
    margin-top: 0;
    margin-left: auto;
    width: auto;
  }
  .u-header .u-nav-2 {
    margin-top: 20px;
    margin-bottom: -305px;
    margin-right: 21px;
  }
}</style>
