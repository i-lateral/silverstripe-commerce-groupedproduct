<% require javascript("framework/thirdparty/jquery/jquery.js") %>
<% require javascript("commerce-groupedproduct/javascript/ChildProductSelect.js") %>

<ul id="$ID" class="$extraClass">
	<% loop $Options %>
		<li class="$Class">
			<input
                id="$ID"
                class="radio"
                name="$Name"
                type="radio"
                value="$Value"
                <% if $isChecked %>checked<% end_if %>
                <% if $isDisabled %>disabled<% end_if %>
                <% if $Price %>data-price="{$Price.Nice}"<% end_if %>
            />
			<label for="$ID">
                $Title
                <% if $PriceDiff %><em>$PriceDiff.Nice</em><% end_if %>
            </label>
		</li>
	<% end_loop %>
</ul>
