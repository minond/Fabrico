<?php

// id on end of url
FabricoURL::matching(FabricoURL::MATCH_ID);
FabricoURL::expects(2);

FabricoURL::updates(array(
	Fabrico::$uri_query_file,
	Fabrico::$uri_query_id
));
