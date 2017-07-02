<!--
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
-->

<!-- Use this image as spacer for not available actions of channels -->
<!-- DEFINE SpacerImg -->
<img src="/images/pix.gif" class="imgbar wide" style="width:16px;height:16px" width="16" height="16" alt="" />
<!-- END DEFINE -->

<div class="ui-tabs">

    <ul>
        <li><a href="#type">{{SelectEntityType}}</a></li>
        <li><a href="#template">{{SelectEntityTemplate}}</a></li>
    </ul>

    <div id="type">

        <table id="typeTable" class="dataTable">
            <thead>
            <tr>
                <th>{{EntityType}}</th>
                <th style="white-space:nowrap">{{ExampleUnit}}</th>
                <th><i class="fa fa-list-ol" title="{{AcceptChildCount}}"></i></th>
                <th></th>
                <th>{{Description}}</th>
                <th>{{Select}}</th>
            </tr>
            </thead>
            <tbody>

            <!-- BEGIN ENTITYTYPES -->
            <tr>
                <td class="icons" style="white-space:nowrap;font-weight:bold">
                    <label for="type-{ID}">
                        <img src="/images/pix.gif" data-src="{ICON}" class="def"
                             style="vertical-align:middle;margin-right:8px" alt="">
                        {NAME}
                    </label>
                </td>
                <td>{UNIT}</td>
                <td class="c">
                    <!-- Add invisible spans for sorting -->
                    <!-- IF {CHILDS} == -1 -->
                        <i class="sort">X</i>&infin;
                    <!-- ELSEIF {CHILDS} == 0 -->-<!-- ELSE -->{CHILDS}<!-- ENDIF -->
                </td>
                <td class="icons">
                    <!-- INCLUDE channeltype.inc.tpl -->
                </td>
                <td style="font-size:smaller">{DESCRIPTION}</td>
                <td>
                    <form action="/channels/add" method="post">
                    <input type="hidden" name="type" value="{ID}" />
                    <input type="submit" value="&raquo;" />
                    </form>
                </td>
            </tr>
            <!-- END -->

            </tbody>

        </table>

        <!-- Legend -->

        <div class="icons legendtip">
            <i class="fa fa-arrows-alt"></i>{{ReadWritableEntity}} &nbsp;
            <i class="fa fa-download"></i>{{WritableEntity}} &nbsp;
            <i class="fa fa-upload"></i>{{ReadableEntity}} &nbsp;
        </div>

    </div>

    <div id="template">

        <table id="tplTable" class="dataTable">
            <thead>
            <tr>
                <th>{{Name}}</th>
                <th>{{Description}}</th>
                <th>{{Select}}</th>
            </tr>
            </thead>
            <tbody>

            <!-- BEGIN TEMPLATES -->
            <tr>
                <td class="icons" style="white-space:nowrap;font-weight:bold">
                    <label for="{FILE}">
                        <img src="/images/pix.gif" data-src="{ICON}" class="def"
                             style="vertical-align:middle;margin-right:8px" alt="" />
                        {NAME}
                    </label>
                </td>
                <td style="font-size:smaller">{DESCRIPTION}</td>
                <td>
                    <form action="/channels/template" method="post">
                    <input type="hidden" name="template" value="{FILE}" />
                    <input type="submit" value="&raquo;" />
                    </form>
                </td>
            </tr>
            <!-- END -->

            </tbody>

            <tfoot>
            <tr>
                <th class="l i" colspan="3"></th>
            </tr>
            </tfoot>

        </table>

        <div class="icons legendtip">
            {{AdjustTemplateAfterwards}}
        </div>

    </div>

</div>
