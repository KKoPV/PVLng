
<table id="tblTypes" class="dataTable">

    <thead>
        <tr>
            <th>{{Id}}</th>
            <th>{{Name}}</th>
            <th>{{ExampleUnit}}</th>
            <th>{{Model}}</th>
            <th>{{Type}}</th>
            <th><i class="fa fa-list-ol" title="{{AcceptChildCount}}"></i></th>
            <th></th>
            <th></th>
        </tr>
    </thead>

    <tbody>
        <!-- BEGIN TYPES -->
        <tr data-id="{ID}">
            <td>{ID}</td>
            <td class="icons">
                <img src="{ICON}" class="channel-icon tip" title="{ICON}">
                <strong>{NAME}</strong>
            </td>
            <td>{UNIT}</td>
            <td>{MODEL}</td>
            <td>{TYPE}</td>
            <td class="c">
                <!-- Add invisible spans for sorting -->
                <!-- IF {CHILDS} == -1 -->
                    <span class="sort">X</span>&infin;
                <!-- ELSEIF {CHILDS} == 0 -->-<!-- ELSE -->{CHILDS}<!-- ENDIF -->
            </td>
            <td class="icons">
                <!-- Add invisible spans for sorting -->
                <!-- IF {READ} AND {WRITE} -->
                    <i class="fa fa-fw fa-arrows-alt"></i>
                <!-- ELSEIF {WRITE} -->
                    <i class="fa fa-fw fa-download"></i>
                <!-- ELSEIF {READ} -->
                    <i class="fa fa-fw fa-upload"></i>
                <!-- ELSE -->
                    <i class="ico pix"></i>
                <!-- ENDIF -->

                <!-- IF {GRAPH} -->
                    <i class="fa fa-fw fa-area-chart"></i>
                <!-- ELSE -->
                    <i class="ico pix"></i>
                <!-- ENDIF -->

                <form action="channel/add" method="post">
                <input type="hidden" name="type" value="{ID}">
                <button class="fa fa-file-o" style="background:none;border:0"></button>
                </form>
            </td>
            <td>
                <span style="display:none">{DESCRIPTION}</span>
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

    <i class="fa fa-area-chart"></i>{{UsableInCharts}} &nbsp;
    <i class="fa fa-file-o"></i>{{CreateChannel}}
    <br>
    <span><img src="/images/ico/plus_circle_frame.png">{{ShowDescription}}</span>
</div>
