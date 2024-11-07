use ppm_image::filters::{ApplyConvolution, BlurFilter, Edges, Intensity};
use ppm_image::Image;
use std::path::Path;


fn main() {
    let _img = Image::load_from_file(Path::new("Chaton.ppm"));

    let blur_filter = BlurFilter::new(3);
    let brighten = Intensity::new(1.2);
    let darken = Intensity::new(0.8);

    // let mut blurred_kitten = img.clone();
    // blurred_kitten.apply(&blur_filter);
    // blurred_kitten.write_to_file("blurred_chaton.ppm");

    let mut brightened_kitten = _img.clone();
    for _ in 0..3 {
        brightened_kitten.apply(&brighten);
    }
    brightened_kitten.write_to_file("brightened_chaton.ppm");

    let mut darkened_kitten = _img.clone();
    for _ in 0..5 {
        darkened_kitten.apply(&darken);
    }
    darkened_kitten.write_to_file("bla.ppm");

    let mut edged_kitten = _img.clone();
    let edges_detection = ApplyConvolution { convolution: Edges };
    edged_kitten.apply(&edges_detection);
    edged_kitten.write_to_file("edged_chaton.ppm");
}
