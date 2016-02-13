<style>
#tv{$tv->id}-cb input[type='checkbox'].obj:checked + label{literal}{color: #3697cd !important;}{/literal}
#tv{$tv->id}-cb input[type='checkbox'].taxonomy-folder-id{literal}{
	display: block;
	position: relative;
	float: left;
	top: 3px;
	margin-right: 5px;}{/literal}
#tv{$tv->id}-cb input[type='checkbox'].taxonomy-folder-id + label{literal}{font-weight: bold;}{/literal}
#tv{$tv->id}-cb input[type='checkbox'].taxonomy-folder-id:checked + label{literal}{color: #17618a !important;}{/literal}
.taxonomy-folder{literal}{
	padding: 10px;
	margin-bottom: 5px;
	border-radius: 3px;
	background-color: #fdfdfd;
	border: 1px solid #e5e5e5;}{/literal}
.taxonomy-vals{literal}{padding-bottom: 10px;}{/literal}
</style>

<script type="text/javascript">
{literal}
function checkparents(child) {
var parentvaluesdiv = child.parentNode;
var parentdiv = parentvaluesdiv.parentNode;
	if (parentdiv) {
		var parentchekbox = parentdiv.querySelector('input.taxonomy-folder-id');
		parentchekbox.checked = true;
		if (parentdiv.id != "tv{/literal}{$tv->id}-cb{$rootid}{literal}") {checkparents(parentdiv);}
	}
}

function uncheckparents(child) {
var parentvaluesdiv = child.parentNode;
var parentdiv = parentvaluesdiv.parentNode;
	if (parentdiv) {
		var a = parentdiv.querySelectorAll("input.obj:checked").length;
			if (a==0) {
				var parentchekbox = parentdiv.querySelector('input.taxonomy-folder-id');
				/*alert(child.id +'-> a0 в:' + parentdiv.id +  ' выключаем:' +parentchekbox.id);*/
				parentchekbox.checked = false;
				if (parentdiv.id != "tv{/literal}{$tv->id}-cb{$rootid}{literal}") {uncheckparents(parentdiv);}
			} else {
				/*var parentchekbox = parentdiv.querySelector('input.taxonomy-folder-id');
				var b = parentdiv.querySelector("input.obj:checked");
				alert(child.id +'-> есть '+a+' ('+b.id+'...) отмеченные в '+ parentdiv.id + ' не трогаем ' +  parentchekbox.id);*/
			}
	}
}
{/literal}
</script>


<div id="tv{$tv->id}-cb">
{foreach from=$allfolders item=folder key=k}{assign var="place" value="0"}
<div id="tv{$tv->id}-cb{$folder.id}" class="taxonomy-folder">

<input type="checkbox" id="tv{$tv->id}-main-{$folder.id}" name="tv{$tv->id}[]" class="taxonomy-folder-id" value="{$folder.id}" {if $folder.checked}checked{/if}></input>
<label for="tv{$tv->id}-main-{$folder.id}">{$folder.name}</label>
<div id="tv{$tv->id}-cb{$folder.id}-vals" class="taxonomy-vals"></div>
<div id="tv{$tv->id}-cb{$folder.id}-childs"></div>
</div>

<script type="text/javascript">


{if $folder.parent != 0}
var parent = document.getElementById("tv{$tv->id}-cb{$folder.parent}-childs");
var child = document.getElementById("tv{$tv->id}-cb{$folder.id}");
parent.appendChild(child);
{/if}
// <![CDATA[

{literal}
Ext.onReady(function() {
  var {/literal}fld{$folder.id}{literal} = MODx.load({
   {/literal}  
        xtype: 'checkboxgroup'
        ,id: 'tv{$tv->id}-{$folder.id}'
        ,name: 'tv-{$tv->id}-{$folder.id}'
        ,vertical: true
        ,columns: {if $params.columns}{$params.columns}{else}1{/if}
        ,renderTo: 'tv{$tv->id}-cb{$folder.id}-vals'
        ,width: '99%'
        ,allowBlank: {if $params.allowBlank == 1 || $params.allowBlank == 'true'}true{else}false{/if}
  
        ,hideMode: 'offsets'
        ,msgTarget: 'under'
				,listeners: {literal}{ 
            change: function(){{/literal}
            var a = document.querySelectorAll("#tv{$tv->id}-{$folder.id} input[type='checkbox']:checked").length;

            if (a==0) {literal}{{/literal}
            	var me = document.getElementById("tv{$tv->id}-{$folder.id}");
            	uncheckparents(me);
            {literal}}{/literal}else {literal}{{/literal}
            	var me = document.getElementById("tv{$tv->id}-{$folder.id}");
            	checkparents(me);
	          {literal}}{/literal}
                
           {literal} },
            scope:this
        }{/literal}
        
        ,items: [
    	    {foreach from=$opts item=item key=ke name=cbs}
    	    	{if $item.parent == $folder.id}{assign var="place" value="1"}
      			 {literal}{{/literal}
           	  name: 'tv{$tv->id}[]'
           	  ,id: 'tv{$tv->id}-{$folder.id}-{$item.value}'
         	    ,boxLabel: '{$item.text|escape:"javascript"}'
           	  ,checked: {if $item.checked}true{else}false{/if}
          	  ,inputValue: {$item.value}
         	    ,value: {$item.value}
            	,cls: 'obj'
     		  	{literal}}{/literal}{if NOT $smarty.foreach.cbs.last},{/if}
      		 {/if}
      		{/foreach}]
    {literal}}{/literal});
   
    {foreach from=$opts item=item key=ke name=cbs}
	    {if $item.parent == $folder.id}
		    Ext.getCmp('tv{$tv->id}-{$folder.id}-{$item.value}').on('check',MODx.fireResourceFormChange);
 	   {/if}
    {/foreach}

	{if $place == "1"}
    Ext.get('tvdef{$tv->id}').dom.value = "{$cbdefaults}";
    Ext.getCmp('modx-panel-resource').getForm().add(fld{$folder.id});
	{/if}

/*.x-column-inner fix*/	
{if $widthfix == true}	
	var boxwidth = document.getElementById("modx-container").offsetWidth;
	boxwidth = boxwidth - 625;
	    Ext.getCmp('tv{$tv->id}-{$folder.id}').setWidth(boxwidth);
{/if}
	
});


// ]]>
</script>
{/foreach}
</div>

