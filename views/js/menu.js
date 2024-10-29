/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Simul Digital
 * @copyright 2016-2024 Simul Digital
 * @license LICENSE.txt
 */

$(window).ready(function () {
    moduleAdminLink = moduleAdminLink.replace(/\amp;/g, '');
    window.vMenu = new Vue({
        el: '#sdyourio-menu',
        data: {
            selectedTabName: currentPage,
        },
        methods: {
            makeActive: function (item) {
                this.selectedTabName = item;
                window.history.pushState({}, '', moduleAdminLink + '&currentPage=' + item);
            },
            isActive: function (item) {
                if (this.selectedTabName == item) {
                    setTimeout(function () {
                        $('.sdyourio_menu').addClass('sd-menu-hide');
                        $('#' + item).removeClass('sd-menu-hide');
                        $('[data-toggle="tooltip"]').tooltip();
                    }, 100)

                    return true;
                }
            },
        }
    });
});