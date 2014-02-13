<?php
// Specify the profile fields to be displayed here
// Full list of field names: 
// profile_address1, profile_address2, profile_city, profile_country, profile_postal_code, profile_phone, profile_website, profile_aboutme

$profile_fields = array('profile_phone','profile_website','profile_aboutme');

// Show Owner Profile Picture
$profilepicture_loaded = jimport('mosets.profilepicture.profilepicture');

if( $profilepicture_loaded )
{
	$profilepicture = new ProfilePicture($this->owner->id);
	
	if( $profilepicture->exists() )
	{
		echo '<img src="'.$profilepicture->getURL(PROFILEPICTURE_SIZE_200).'" alt="'.$this->owner->username.'" style="float:left;margin:0 1em 2em 0"/>';
	}
	else
	{
		echo '<img src="'.$profilepicture->getFillerURL(PROFILEPICTURE_SIZE_200).'" alt="'.$this->owner->username.'" style="float:left;margin:0 1em 2em 0"/>';
	}
}

// Show Owner Profile
echo '<dl>';
foreach ($profile_fields as $profile_field) :
	if( !isset($this->user_profile_fields[$profile_field]) )
	{
		continue;
	}
	$profile = $this->user_profile_fields[$profile_field];
	if ($profile->value) :
		echo '<dt class="'.$profile_field.'">'.$profile->label.'</dt>';
		$profile->text = htmlspecialchars($profile->value, ENT_COMPAT, 'UTF-8');

		switch ($profile->id) :
			case "profile_website":
				$v_http = substr ($profile->profile_value, 0, 4);
				echo '<dd class="'.$profile_field.'">';
				if ($v_http == "http") :
					echo '<a href="'.$profile->text.'">'.$profile->text.'</a>';
				else :
					echo '<a href="http://'.$profile->text.'">'.$profile->text.'</a>';
				endif;
				echo '</dd>';
				break;

			default:
				echo '<dd class="'.$profile_field.'">'.$profile->text.'</dd>';
				break;
		endswitch;
	endif;
endforeach;
echo '</dl>';

?>