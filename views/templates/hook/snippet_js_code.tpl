{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
* @author    YOURAI
* @copyright 2016-2024 YOURAI
* @license   LICENSE.txt
**}

{literal}
    <script type="text/javascript">
        window.your = window.your || {};
        window.your.config = {
            matchId: '{/literal}{$current_product->ean13},{$current_product->mpn}{literal}',
            locale: '{/literal}{$locale}{literal}',
            subpath: 'your-app'
        };
    </script>
{/literal}

