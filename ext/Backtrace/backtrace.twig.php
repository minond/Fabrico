<style type="text/css">
.fabrico-error {
    border: 1px solid rgb(248, 234, 178);
    background-color: rgb(255, 254, 249);
    margin: 18px 10px 0px 10px;
    padding: 10px;
    font: 13px Arial;
    cursor: default;
}

.fabrico-error .header {
    border-bottom: 1px solid rgb(248, 234, 178);
    margin: -10px -10px 10px -10px;
    padding: 10px;
    font-size: 16px;
    color: rgb(250, 60, 60);
    font-weight: bold;
}

.fabrico-error .section-header {
    margin-top: 10px;
    margin-bottom: 5px;
    padding-bottom: 5px;
    border-bottom: 1px solid rgb(255, 249, 216);
    font-weight: bold;
}

.fabrico-error .source {
    font: 12px monospace;
    background-color: white;
    margin-right: -10px;
    margin-left: -10px;
    margin-top: 10px;
    border-top: 1px solid rgb(248, 234, 178);
    border-bottom: 1px solid rgb(248, 234, 178);
    padding: 4px 0px 4px 10px;
    white-space: pre-line;
}

.fabrico-error .source table {
    border-collapse: collapse;
}

.fabrico-error .source table td.source-num {
    width: 40px;
    padding-right: 10px;
    text-align: right;
    color: gray;
}

.fabrico-error .source table tr.error-line td.source-text {
    color: rgb(235, 7, 7);
}
</style>
<div class="fabrico-error">
    <div class="header">Application error ({{ errtype }})</div>

    <div class="section-header">Description</div>
    <div class="desc">
        <div>{{ file }}, line number {{ line }}</div>
        <div>{{ message }}</div>
    </div>
    <div class="source">
        <table>
            {% for s_line in source %}
            {% if s_line.num == line %}
            <tr class="error-line">
            {% else %}
            <tr>
            {% endif %}
                <td class="source-num">{{ s_line.num }}</td>
                <td class="source-text">
                    <pre>{{ s_line.text }}</pre>
                </td>
            </tr>
            {% endfor %}
        </table>
    </div>

    <div class="section-header">Backtrace</div>
    <div class="backtrace">
        <ol>
        {% for trace in backtrace %}
            {% if trace.file and loop.index != 1 %}
            <li>
                <span>[{{ trace.file }}:{{ trace.line }}]</span>
                <span>- <b>{% if trace.class %}{{ trace.class }}::{% endif %}{{ trace.function }}</b></span>
            </li>
            {% endif %}
        {% endfor %}
        </ol>
    </div>
</div>
