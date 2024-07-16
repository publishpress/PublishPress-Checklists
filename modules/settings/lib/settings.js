jQuery(document).ready(function ($) {
  // Tabs

  var $tabsWrapper = $('#publishpress-checklists-settings-tabs');
  $tabsWrapper.find('li').click(function (e) {
    e.preventDefault();
    $tabsWrapper.children('li').filter('.nav-tab-active').removeClass('nav-tab-active');
    $(this).addClass('nav-tab-active');

    var panel = $(this).find('a').first().attr('href');

    if (browserSupportStorage()) {
      saveStorageData('ppch_settings_active_tab', panel.slice(1));
    }

    $('table[id^="ppch-"]').hide();
    $(panel).show();
  });

  var ppchTab = String($tabsWrapper.find('li:first-child a').attr('href').slice(1));

  if (typeof ppchSettings != 'undefined' && typeof ppchSettings.tab != 'undefined') {
    ppchTab = ppchSettings.tab;
    $('#publishpress-checklists-settings-tabs a[href="#' + ppchTab + '"]').click();
  } else if (browserSupportStorage() && getStorageData('ppch_settings_active_tab')) {
    ppchTab = getStorageData('ppch_settings_active_tab');
    $('#publishpress-checklists-settings-tabs a[href="#' + ppchTab + '"]').click();
  }

  var $hiddenFields = $('input[id^="ppch-tab-"]');

  $hiddenFields.each(function () {
    var $this = $(this);
    var $wrapper = $this.next('table');
    $wrapper.attr('id', $this.attr('id'));
    $this.remove();

    if ($wrapper.attr('id') !== ppchTab) {
      $wrapper.hide();
    }
  });

  /**
   * Check if browser support local storage
   * @returns
   */
  function browserSupportStorage() {
    if (typeof Storage !== 'undefined') {
      return true;
    } else {
      return false;
    }
  }
  /**
   * Save local storage data
   * @param {*} storageName
   * @param {*} storageValue
   */
  function saveStorageData(storageName, storageValue) {
    removeStorageData(storageName);
    window.localStorage.setItem(storageName, JSON.stringify(storageValue));
  }

  /**
   * Get local storage data
   * @param {*} storageName
   * @returns
   */
  function getStorageData(storageName) {
    return JSON.parse(window.localStorage.getItem(storageName));
  }

  /**
   * Remove local storage data
   * @param {*} storageName
   */
  function removeStorageData(storageName) {
    window.localStorage.removeItem(storageName);
  }
});
