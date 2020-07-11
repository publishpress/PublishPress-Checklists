/**
 * @package PublishPress
 * @author PublishPress
 *
 * Copyright (C) 2020 PublishPress
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

(function ($, window, document, PP_Checklists, PPCH_WooCommerce) {
    'use strict';

    $(function () {

        /**
         *
         * Yoast Readability Analysis
         *
         */
        if ($('#pp-checklists-req-yoast_readability_analysis').length > 0) {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var readabilityAnalysisPass = false;

                /* "yoast_wpseo_content_score" ==> Yoast seo input for readbility score
                 * 71 - 100 ==> Yoast readability grade for pass/green color
                 */
                if ($('#yoast_wpseo_content_score').length === 0) {
                    return;
                }

                var obj = $('#yoast_wpseo_content_score').val();

                if (typeof obj !== 'undefined') {

                    if (obj >= '71') {
                        readabilityAnalysisPass = true;
                    }

                    $('#pp-checklists-req-yoast_readability_analysis').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        readabilityAnalysisPass
                    );

                }
            });

        }

        /**
         *
         * Yoast Seo Analysis
         *
         */
        if ($('#pp-checklists-req-yoast_seo_analysis').length > 0) {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var seoAnalysisPass = false;

                /* "yoast_wpseo_linkdex" ==> Yoast seo input for seo score
                 * 71 - 100 ==> Yoast seo grade for pass/green color
                 */
                if ($('#yoast_wpseo_linkdex').length === 0) {
                    return;
                }

                var obj = $('#yoast_wpseo_linkdex').val();

                if (typeof obj !== 'undefined') {

                    if (obj >= '71') {
                        seoAnalysisPass = true;
                    }

                    $('#pp-checklists-req-yoast_seo_analysis').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        seoAnalysisPass
                    );

                }
            });

        }

    });

})(jQuery, window, document, PP_Checklists);
