<?php

namespace Fabrico\Test;

/**
 * tests helper
 */
trait DataGenerator {
	private function rand_name () {
		$names = [
			'Kristeen', 'Alley', 'Jackelyn', 'Cheatham', 'Cassaundra', 'Ferrari',
			'Chi', 'Burkett', 'Prince', 'Donohue', 'Kristian', 'Bryson', 'Caroyln',
			'Scanlon', 'Trudy', 'Lankford', 'Cuc', 'Harwood', 'Melia', 'Howe',
			'Nigel', 'Fierro', 'Kortney', 'Sammons', 'Oren', 'Hefner', 'Gerry',
			'Sage', 'Apryl', 'Harder', 'Karina', 'Wilde', 'Coletta', 'Ledbetter',
			'Ashli', 'Osorio', 'Lanette', 'Nickerson', 'Jarod', 'Malcolm', 'Nilda',
			/*'Colby', 'Rikki', 'Whittaker', 'Thea', 'Redman', 'Vashti', 'Gauthier',
			'Elida', 'Elder', 'Genevie', 'Clough', 'Mei', 'Chase', 'Shara', 'Lorenzo',
			'Sharie', 'Coley', 'Estell', 'Salley', 'Chung', 'Beatty', 'Moira', 'Travis',
			'Maxima', 'London', 'Micha', 'Faber', 'Madelaine', 'Mcqueen', 'Shanita',
			'Cardwell', 'Elvin', 'Barba', 'Felicita', 'Larsen', 'Georgina', 'Meier',
			'Etsuko', 'Gillis', 'Krystina', 'Darby', 'Kyoko', 'Stiles', 'Tamekia',
			'Kirk', 'Bryanna', 'Arce', 'Bee', 'Carlisle', 'Ngan', 'Hurley', 'Annita',
			'Pino', 'Sidney', 'Luciano', 'Lenard', 'York', 'Ute', 'Fortin', 'Latanya',
			'Leigh', 'Ja', 'Ferreira', 'Ardelle', 'Fanning', 'Desire', 'Rosenthal',
			'Renata', 'Rawls', 'Maxwell', 'Dobson', 'Sherrell', 'Ault', 'Mora', 'Peachey',
			'Kazuko', 'See', 'Caitlin', 'Brothers', 'Isabell', 'Watters', 'Libbie', 'Huey',
			'Lino', 'Larry', 'Artie', 'Carden', 'Kathe', 'Maness', 'Caryl', 'Harness',
			'Kirby', 'Robison', 'Broderick', 'Hartmann', 'Dean', 'Willingham', 'Shalanda',
			'Phan', 'Jefferson', 'Council', 'Lilliam', 'Durr', 'Toney', 'Heflin', 'Lorrie',
			'Aponte', 'Jeraldine', 'Houghton', 'Gale', 'Ash', 'Louella', 'Lott', 'Carina',
			'Pate', 'Kati', 'Priest', 'Jennefer', 'Flemming', 'Loreen', 'Folse', 'Francesco',*/
			'France', 'Curt', 'South', 'Lisha', 'Lavoie', 'Merideth', 'Tompkins', 'Min',
			'Lafleur', 'Krysten', 'Morrill', 'Vern', 'Jacoby', 'Leonardo', 'Alston', 'Laveta',
			'Dias', 'Jeromy', 'Purdy', 'Star', 'Moyer', 'Milan', 'Kiefer', 'Cherryl', 'Sisk',
			'Elli', 'Hutton', 'Chara', 'Buckner', 'Elidia', 'Sample', 'Rosella', 'Tobias',
			'Elodia', 'Hammer', 'Shaniqua', 'Jameson'
		];

		return $names[ $this->rand_int(1, count($names)) - 1 ];
	}

	private function rand_int ($min = null, $max = null) {
		return mt_rand($min, $max);
	}
}
