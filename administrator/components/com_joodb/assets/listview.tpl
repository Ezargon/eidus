<!-- JooDatabase: initial template for new databases  -->
<div style="padding: 5px; margin: 10px 0; border: 1px solid #ccc; background-color: #f8fdbb;">
    <h3>Some important hints about template editing</h3>
    <ul>
        <li>Disable code cleaning of your editor. Otherwise some arguments may be lost.</li>
        <li>JooDB has a built in code editor. You can switch it on with the general options of JooDB.</li>
        <li>Don't edit css here. Use your template css or the separate css file /components/com_joodb/assets/joodb.css</li>
        <li>You find the default templates in /administrator/components/com_joodb/assets/</li>
        <li>Remember [joodb field|FIELDNAME] is replaced by field content.</li>
        <li>You can also use the select boxes below the editor elements.</li>
        <li>You must have two [joodb loop] arguments in the cataloge template.</li>
    </ul>
    <p><small>This text is part of the JooDB default template for lists in
            administrator/components/com_joodb/listview.tpl. Simply delete this in
            the catalog-view template of your database.</small></p>
</div>
<!-- see comomponents/com_joodb/assets/joodb.css for style definitions -->
<!-- Search box -->
{joodb searchbox|title,value,category}
{joodb alphabox}
<div style="float:right;">{joodb limitbox}</div>
<p>{joodb pagecount}</p>
<!-- Title with Sortlinks -->
<table>
    <thead>
    <tr>
        <th>#C_DEFAULT_HEADER</th>
    </tr>
    </thead>
</table>
<!-- LOOP Start -->
{joodb loop}
#C_DEFAULT_LOOP
{joodb loop}
<!-- LOOP End -->
<h3>{joodb nodata}</h3>
<!-- LOOP Pagination -->
<p>{joodb pagecount}</p>
<div class="pagination">{joodb pagenav}</div>

