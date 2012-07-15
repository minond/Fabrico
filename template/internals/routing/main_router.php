<?php

// id on end of url
FabricoURL::rule(2, FabricoURL::MATCH_ID, array(
	Fabrico::$uri_query_file,
	Fabrico::$uri_query_id
));
