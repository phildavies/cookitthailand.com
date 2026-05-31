Route66SeoAnalyzer = window.Route66SeoAnalyzer || {};

(function (Route66SeoAnalyzer, $) {
  'use strict';

  Route66SeoAnalyzer.start = function () {

    // Options
    this.options = Joomla.getOptions('Route66SeoAnalyzerOptions');

    // Analyzer
    this.analyzer = new Route66Seo(this.options);

    this.$keywordField = $(this.options.fields.keyword);
    this.$titleField = $(this.options.fields.title);
    if (this.options.fields.pagetitle) {
      this.$pageTitleField = $(this.options.fields.pagetitle);
    }
    this.$aliasField = $(this.options.fields.alias);
    this.currentAlias = this.$aliasField.val();
    this.$descriptionField = $(this.options.fields.description);
    this.$scoreField = $(this.options.fields.score);
    this.$readabilityField = $(this.options.fields.readability);
    this.$languageField = $(this.options.fields.language);

    if (this.options.option === 'com_k2') {
      this.$deleteImageCheckbox = $('#del_image');
      this.$uploadImageField = $('input[name="image"]');
      this.$existingImageField = $('#existingImageValue');
      this.$imageCaptionField = $('input[name="image_caption"]');
    }

    if (this.options.option === 'com_content') {
      this.$introImage = $('#jform_images_image_intro');
      this.$introImageAlt = $('#jform_images_image_intro_alt');
      this.$fullImage = $('#jform_images_image_fulltext');
      this.$fullImageAlt = $('#jform_images_image_fulltext_alt');
    }

    if (this.options.position === 'toolbar' || this.options.position === 'sidebar') {

      this.$keywordField.val(this.options.keywordValue);

      var scoreFieldClone = this.$scoreField.clone();
      scoreFieldClone.removeAttr('id').attr('type', 'hidden');

      var readabilityFieldClone = this.$readabilityField.clone();
      readabilityFieldClone.removeAttr('id').attr('type', 'hidden');

      var keywordFieldClone = this.$keywordField.clone();
      keywordFieldClone.removeAttr('id').attr('type', 'hidden');

      $('form[name="adminForm"]').append(scoreFieldClone);
      $('form[name="adminForm"]').append(keywordFieldClone);
      $('form[name="adminForm"]').append(readabilityFieldClone);
      this.clones = {
        $scoreField: scoreFieldClone,
        $keywordField: keywordFieldClone,
        $readabilityField: readabilityFieldClone
      };
    }

    // Editor
    if (this.options.editor === 'tinymce') {
      this.editor = tinymce.get(this.options.fields.text.substr(1));
    } else if (this.options.editor === 'jce') {
      this.editor = tinymce.getInstanceById(this.options.fields.text.substr(1));
    } else {
      this.editor = null;
      this.$contentField = $(this.options.fields.text);
    }

    // Add events
    this.addEvents();

    // Run
    this.analyze();
  };

  Route66SeoAnalyzer.addEvents = function () {
    this.$keywordField.on('change', $.proxy(this.analyze, this));
    this.$titleField.on('change', $.proxy(this.analyze, this));
    if (this.$pageTitleField) {
      this.$pageTitleField.on('change', $.proxy(this.analyze, this));
    }
    this.$aliasField.on('change', $.proxy(this.analyze, this));
    this.$descriptionField.on('change', $.proxy(this.analyze, this));
    if (this.options.editor === 'tinymce') {
      this.editor.on('change', $.proxy(this.analyze, this));
    } else if (this.options.editor === 'jce') {
      this.editor.onChange.add($.proxy(this.analyze, this));
    } else {
      this.$contentField.on('change', $.proxy(this.analyze, this));
    }

    if (this.options.multilanguage) {
      this.$languageField.on('change', $.proxy(this.setLocale, this));
    }

    if (this.options.option === 'com_k2') {
      this.$deleteImageCheckbox.on('change', $.proxy(this.analyze, this));
      this.$uploadImageField.on('change', $.proxy(this.analyze, this));
      this.$existingImageField.on('change', $.proxy(this.analyze, this));
      this.$imageCaptionField.on('change', $.proxy(this.analyze, this));
    }

    if (this.options.option === 'com_content') {
      this.$introImage.on('change', $.proxy(this.analyze, this));
      this.$fullImage.on('change', $.proxy(this.analyze, this));
      this.$introImageAlt.on('change', $.proxy(this.analyze, this));
      this.$fullImageAlt.on('change', $.proxy(this.analyze, this));
    }

  };

  Route66SeoAnalyzer.analyze = function () {
    var self = this;

    this.analyzer.analyze(this.getPaperText(), this.getPaperAttributes()).then(function () {
      var score = self.analyzer.score;
      self.$scoreField.val(score);
      var readability = self.analyzer.readabilityScore;
      self.$readabilityField.val(readability);
      if (self.clones) {
        self.clones.$scoreField.val(score);
        self.clones.$readabilityField.val(readability);
        self.clones.$keywordField.val(self.$keywordField.val());
      }
    });
  };

  Route66SeoAnalyzer.getPaperText = function () {
    var text = '';
    if (this.options.option === 'com_k2') {
      if (this.$deleteImageCheckbox.length || this.$uploadImageField.val() || this.$existingImageField.val()) {
        text += '<img alt="' + (this.$imageCaptionField.val() || this.$titleField.val()) + '" />';
      }
    }
    if (this.options.option === 'com_content') {
      if (this.$introImage.val()) {
        text += '<img alt="' + this.$introImageAlt.val() + '" />';
      }
      if (this.$fullImage.val()) {
        text += '<img alt="' + this.$fullImage.val() + '" />';
      }
    }

    if (this.editor) {
      text += this.editor.getContent();
    } else {
      text += this.$contentField.val();
    }
    var readmore = 'id="system-readmore';
    if (this.options.split && text.indexOf(readmore) !== -1) {
      var parts = text.split(readmore);
      text = parts[1];
    }

    return text;
  };

  Route66SeoAnalyzer.getPaperAttributes = function () {
    var title;
    if (this.$pageTitleField) {
      title = this.options.overrides.title || this.$pageTitleField.val() || this.$titleField.val();
    } else {
      title = this.options.overrides.title || this.$titleField.val();
    }
    if (this.options.sitename_in_title == 1) {
      title = this.options.sitename + ' - ' + title;
    } else if (this.options.sitename_in_title == 2) {
      title += ' - ' + this.options.sitename;
    }

    var locale = this.options.locale;
    if (this.options.multilanguage) {
      var value = this.$languageField.val();
      if (value != '*' && value != '') {
        locale = value;
      }
    }

    var attributes = {
      keyword: this.$keywordField.val(),
      description: this.options.overrides.description || this.$descriptionField.val(),
      title: title,
      slug: this.getSlug(),
      permalink: this.getUrl(),
      locale: locale
    };

    return attributes;
  };

  Route66SeoAnalyzer.getUrl = function () {
    return this.options.site + this.getSlug();
  };

  Route66SeoAnalyzer.getSlug = function () {

    var url = this.options.url;

    if (url.startsWith('index.php/')) {
      url = url.substr(10);
    }

    url = url.replace(this.currentAlias, this.options.aliasToken);
    url = url.replace(this.options.aliasToken, this.$aliasField.val());

    return url;
  };

  Route66SeoAnalyzer.setLocale = function () {

    const language = this.$languageField.val();

    if (language === '*') {
      return;
    }

    if (language === '') {
      return;
    }

    this.analyzer.setLocale(language);
  }

}(Route66SeoAnalyzer, jQuery));


window.addEventListener('load', function (event) {
  // Delay for JCE...
  window.setTimeout(function () {
    Route66SeoAnalyzer.start();
  }, 700);
});

window.addEventListener('DOMContentLoaded', function (event) {

  var body = jQuery('body');

  jQuery('#route66-seo-dropdown-button').on('click', function (event) {
    event.preventDefault();
    body.toggleClass('route66-seo-dropdown-opened');
    body.removeClass('route66-readability-dropdown-opened');
  });
  jQuery('#route66-seo-dropdown-overlay').on('click', function (event) {
    event.preventDefault();
    body.removeClass('route66-seo-dropdown-opened');
    body.removeClass('route66-readability-dropdown-opened');
  });
  jQuery(document).keypress(function (event) {
    if (event.which == 13 && body.hasClass('route66-seo-dropdown-opened')) {
      event.preventDefault();
      body.removeClass('route66-seo-dropdown-opened');
      body.removeClass('route66-readability-dropdown-opened');
    }
  });

  jQuery('#route66-readability-dropdown-button').on('click', function (event) {
    event.preventDefault();
    body.toggleClass('route66-readability-dropdown-opened');
    body.removeClass('route66-seo-dropdown-opened');
  });
  jQuery('#route66-readability-dropdown-overlay').on('click', function (event) {
    event.preventDefault();
    body.removeClass('route66-readability-dropdown-opened');
    body.removeClass('route66-seo-dropdown-opened');
  });
  jQuery(document).keypress(function (event) {
    if (event.which == 13 && body.hasClass('route66-readability-dropdown-opened')) {
      event.preventDefault();
      body.removeClass('route66-readability-dropdown-opened');
      body.removeClass('route66-seo-dropdown-opened');
    }
  });

});
