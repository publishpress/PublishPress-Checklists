/**
 * @package PublishPress
 * @author PublishPress
 *
 * Copyright (C) 2018 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * This file is part of PublishPress
 *
 * PublishPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PublishPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PublishPress.  If not, see <http://www.gnu.org/licenses/>.
 */

// Based on the TinyMCE words count display found at /wp-admin/js/post.js
// Ignored if Gutenberg is in use.

(function ($, document, tinymce) {
    'use strict';

  if (typeof wp !== 'undefined' && typeof PP_Checklists !== 'undefined' && !PP_Checklists.is_gutenberg_active()) {
    // We trigger an event to make sure the editor is available.
    $(document).trigger(PP_Checklists.EVENT_TINYMCE_LOADED, tinymce);
  }
  
})(jQuery, document, tinymce);
