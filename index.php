<?php

const FORM_IS_SUBMITTED	= 'FORM_IS_SUBMITTED';
const FORM_LABELS = 'FORM_LABELS';
const FORM_NAME = 'FORM_NAME';
const FORM_VALIDATION_ERRORS = 'FORM_VALIDATION_ERRORS';
const FORM_VALIDATION_RULES	= 'FORM_VALIDATION_RULES';

const VALIDATION_ERROR_ALPHA = 'Het invulveld \'%s\' mag enkel alfabetische tekens bevatten.';
const VALIDATION_ERROR_EMAIL = 'Het invulveld \'%s\' moet een geldig e-mailadres zijn.';
const VALIDATION_ERROR_IDENTICAL = 'Het invulveld \'%s\' moet identiek zijn aan veld \'%s\'.';
const VALIDATION_ERROR_INTEGER = 'Het invulveld \'%s\' moet een geheel getal zijn.';
const VALIDATION_ERROR_LENGTH_MAX = 'Het invulveld \'%s\' mag maximaal %d tekens bevatten.';
const VALIDATION_ERROR_LENGTH_MIN = 'Het invulveld \'%s\' moet minstens %d tekens bevatten.';
const VALIDATION_ERROR_REQUIRED = 'Vul het invulveld \'%s\' in.';
const VALIDATION_ERROR_UNKNOWN = 'Validatieregel is onbekend.';

const VALIDATION_RULE_ALPHA = 'VALIDATION_RULE_ALPHA';
const VALIDATION_RULE_EMAIL = 'VALIDATION_RULE_EMAIL';
const VALIDATION_RULE_IDENTICAL = 'VALIDATION_RULE_IDENTICAL';
const VALIDATION_RULE_INTEGER = 'VALIDATION_RULE_INTEGER';
const VALIDATION_RULE_LENGTH_MAX = 'VALIDATION_RULE_LENGTH_MAX';
const VALIDATION_RULE_LENGTH_MIN = 'VALIDATION_RULE_LENGTH_MIN';
const VALIDATION_RULE_REQUIRED = 'VALIDATION_RULE_REQUIRED';

$formRegistration = [
    FORM_IS_SUBMITTED => FALSE,
    FORM_LABELS => [
        'id-number' => 'ID-nummer', 
        'username' => 'gebruikersnaam', 
        'email' => 'e-mailadres',
        'password' => 'wachtwoord', 
        'password-confirm' => 'wachtwoord bevestigen'
        ],
    FORM_NAME => 'form-registration',
    FORM_VALIDATION_ERRORS => [],
    FORM_VALIDATION_RULES => [
        'id-number' => [
            VALIDATION_RULE_REQUIRED,
            VALIDATION_RULE_INTEGER,
            [VALIDATION_RULE_LENGTH_MIN => 6]
        ],
        'username' => [
            VALIDATION_RULE_ALPHA,
            [VALIDATION_RULE_LENGTH_MIN =>8],
            [VALIDATION_RULE_LENGTH_MAX => 12]
        ],
        'email' => [
            VALIDATION_RULE_REQUIRED,
            VALIDATION_RULE_EMAIL
        ],
        'password' => [
            VALIDATION_RULE_REQUIRED,
            [VALIDATION_RULE_IDENTICAL => 'password-confirm']
        ],
        'password-confirm' => [
            VALIDATION_RULE_REQUIRED,
            [VALIDATION_RULE_IDENTICAL => 'password']
        ]
    ]
];

$data = filter_input_array(INPUT_POST);  
//wanneer de post gelukt is kan de count de elementen uit de array tellen
//wanneer de count gefailt is is $data = null dus dan kan er ook niks getelt worden
//en zal de foreach eronder niet uitgevoerd worden
if (count($data)):   
	//CHECK ALLE WAARDEN INGEVULD
	
    foreach ($formRegistration[FORM_VALIDATION_RULES] as $id => $validation) {
    	//uit de array $formRegistration gebruiken we de array FORM_VALIDATION_RULES 
    	//$id staat voor id-number, username, email, password, password-confirm en 
    	//$validation  word de array met de validation rules in mee bedoeld

    	// VALIDATION_ERROR_ALPHA // check if value is only letters
		if (in_array(VALIDATION_RULE_ALPHA, $validation) && !empty($data[$id]) && isset($data[$id])) {
			//in_array(needle, haystack) -> je zoekt de naald in het hooi
			if(!preg_match('/^[a-z]+$/i', $data[$id]) ){
				//preg_match bevat de array volgende tekens  
				$field = $formRegistration[FORM_LABELS][$id];
				//hiermee geef je de waarde terug van welk input veld het is
				$errors[$id] = sprintf(VALIDATION_ERROR_ALPHA, $field);
				//sprintf -> geformateerde string terug krijgen
				//const VALIDATION_ERROR_ALPHA = 'Het invulveld \'%s\' mag enkel alfabetische tekens bevatten.';
				//de '%s' word veranderd door de string die $field meegeeft
			}
		}

		// VALIDATION_ERROR_EMAIL // Check if valid e-mail
		if (in_array(VALIDATION_RULE_EMAIL, $validation) && !empty($data[$id]) && isset($data[$id])) {
			//wanneer de input validation_rule_email bevat dus enkel bij email veld dan word volgende uitgevoerd 
			if(!filter_var($data[$id], FILTER_VALIDATE_EMAIL)) {
				//er word gekeken bij de input bij het emailveld of de string een email adres kan zijn
				//FILTER_VALIDATE_EMAIL is een standaard php functie die kijkt of het een @ bevat en eindigd op .be of .nl ,.
     			$field = $formRegistration[FORM_LABELS][$id];
     			//same as before
				$errors[$id] = sprintf(VALIDATION_ERROR_EMAIL, $field);
				//same as before
     		}
		}

		// VALIDATION_ERROR_IDENTICAL // check if two values are identical
		if (in_array(VALIDATION_RULE_IDENTICAL, $validation) && !empty($data[$id]) && isset($data[$id])) {


		}

		// VALIDATION_ERROR_INTEGER // check integer is really an INT
		if (in_array(VALIDATION_RULE_INTEGER, $validation) && !empty($data[$id]) && isset($data[$id])) {
			//checkt of er validatoin_rule_integer te vinden is bij de input -> enkel bij id in dit geval
			if (!preg_match('/^[0-9]+$/', $data[$id])) {
				//wanneer zo een tekens er niet instaan
				$field = $formRegistration[FORM_LABELS][$id];
				$errors[$id] = sprintf(VALIDATION_ERROR_INTEGER, $field);
				//const VALIDATION_ERROR_INTEGER = 'Het invulveld \'%s\' moet een geheel getal zijn.';
				//%s word vervangen door de waarde van $field wat in dit geval ID-nummer is 

			}
		}

		foreach ($validation as $key => $value) {
			// check of waarde een array is zoals bij LENGTH_MAX, LENGTH_MIN & RULE_REQUIRED
			// regels 37 vanaf hier zijn sommige $values ook een array dit checkt of dit zo is 
			if(is_array($value)){
				//wanneer dit inderdaad een array is en dus een $value bevat gebeurd het volgende
				// VALIDATION_ERROR_LENGTH_MAX // check max characters
				if (key($value) == VALIDATION_RULE_LENGTH_MAX && !empty($data[$id]) && isset($data[$id])) {
					//check max # characters
					if(strlen($data[$id]) > $value[VALIDATION_RULE_LENGTH_MAX]){
						//wanneer er teveel tekens zijn
						$field = $formRegistration[FORM_LABELS][$id];
						$errors[$id] = sprintf(VALIDATION_ERROR_LENGTH_MAX, $field, $value[VALIDATION_RULE_LENGTH_MAX]);
					}
				}

				// VALIDATION_ERROR_LENGTH_MIN // check min characters
				if ( key($value) == VALIDATION_RULE_LENGTH_MIN && !empty($data[$id]) && isset($data[$id])) {
					//zelfde als ervoor maar met te weinig tekens
					//check min # characters
					if(strlen($data[$id]) < $value[VALIDATION_RULE_LENGTH_MIN]){
						$field = $formRegistration[FORM_LABELS][$id];
						$errors[$id] = sprintf(VALIDATION_ERROR_LENGTH_MIN, $field, $value[VALIDATION_RULE_LENGTH_MIN]);
					}
				}

				// VALIDATION_ERROR_IDENTICAL // check if two values are identical
				if (key($value) == VALIDATION_RULE_IDENTICAL && !empty($data[$id]) && isset($data[$id])) {
					//check if identical values
					if($data[$id] !== $data[$value[VALIDATION_RULE_IDENTICAL]]){
						//wanneer de wachtwoorden niet overeenkomen
						$field = $formRegistration[FORM_LABELS][$id];
						$errors[$id] = sprintf(VALIDATION_ERROR_IDENTICAL, $field, $value[VALIDATION_RULE_IDENTICAL]);
					}
				}

			}
		}

    	// VALIDATION_ERROR_REQUIRED // check if field is required
    	if (in_array(VALIDATION_RULE_REQUIRED, $validation) && empty($data[$id]) && isset($data[$id])) {
    		//wanneer het required field leeg is 
    		$field = $formRegistration[FORM_LABELS][$id];
    		$errors[$id] = sprintf(VALIDATION_ERROR_REQUIRED, $field);
		}

		// TEST indien validatieregel leeg zou zijn.
		//$errors[0] = '';
    }

endif;

// array_push()
// implode()
// reset()

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
	<style>
		.input-group-sm {
			margin-bottom: 20px;
		}
		label {
			display: block;
		}
		.error {
			margin-bottom: 20px;
			color: red;
		}
	</style>
</head>
<body>
	<?php
	if (isset($errors)) {
		foreach ($errors as $error) {
			if( !isset($error) || empty($error) ){
				$error = VALIDATION_ERROR_UNKNOWN;
			}

			echo '<p class="error">' . $error . "</p>";
		}
	} else {
		echo "<pre>";
		var_dump($_REQUEST);
		echo "</pre>";

		reset($data);
	}
	?>


	<form action="" method="POST" name="<?= $formRegistration['FORM_LABELS']['id-number']; ?>">
	
		<?php foreach ($formRegistration['FORM_LABELS'] as $id => $label) {
			//voor elk $formRegistration form_labels de $id en de $value
		?>
			<div class="input-group-sm">
				<label for="<?= $id; ?>" class="input-group-addon" id="basic-addon1"><?= $label; ?></label>
				<input class="form-control" id="<?= $id; ?>" type="text" name="<?= $id; ?>" 
    			<?= !empty($data[$id]) ? ' value="' . $data[$id] . '"' : ''; ?>
			>
		</div>
		<?php 
		} ?>
		<input class="form-control" type="submit" name="submit" value="Verzenden">
	</form>
</body>
</html>



<!-- Met dank aan Manon Kindt -->