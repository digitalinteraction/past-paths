function get_img_url(img, size)
{
	var sizes = ['small', 'medium', 'large'];
	return webroot + 'img/artefacts/' + sizes[size] + '/' + img;
}