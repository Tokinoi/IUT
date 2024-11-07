extends LineEdit

var regex = RegEx.new()

func _ready():
	text = Config.rateOfFire
	regex.compile("^.*.[0-9]")

func _on_text_submitted(new_text):
	var result = regex.search(str(new_text))
	if result:
		text = result.get_string()
	else:
		text = ""
	Config.rateOfFire = text

