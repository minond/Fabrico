<f:resource:css>
	div.error_holder {
		cursor: default;
		margin: 20px 10px;
		padding: 9px;
		background-color: white;
		z-index: 1000;
		font: 13px Arial;
		background-color: #fcfca4;
		border: 1px solid red;
		box-shadow: 0px 0px 10px red;
	}

	div.error_holder .title {
		border-bottom: 1px solid black;
		padding-bottom: 7px;
		margin-bottom: 7px;
		font-weight: bold;
	}

	div.error_holder div {
		padding-left: 10px;
		padding-right: 10px;
	}
</f:resource:css>

<div class="error_holder">
	<div class="title">#{title} - #{file}, #{line}</div>
	<div class="message">#{message}</div>
</div>
