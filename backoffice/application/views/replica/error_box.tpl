{if $MESSAGE_DETAILS }
    {if $MESSAGE_STATUS }
        {if $MESSAGE_TYPE }
            {assign var="message_class" value="errorHandler alert alert-success"}
        {else}
            {assign var="message_class" value="errorHandler alert alert-danger"}
        {/if}

        <div id="message_box"  class="{$message_class}" style="padding: 6px">

            <div id="message_note"> 
                <table>
                    <tr>
                        <td>                            
                            {$MESSAGE_DETAILS}
                        </td>
                    </tr>
                </table>
            </div>
            <a href="javascript:void(0)" id= "close_link" class="panel-close pull-right" style="margin-top: -25px;"> <i class="fa fa-2x fa-times"></i></a>
        </div>
    {/if}
{/if}

