pub mod image;

//use std::path::Path;
pub use image::Image; // Import Image struct in lib namespace
pub use image::Pixel;
//use crate::filters::{ApplyConvolution, BlurFilter, Edges, Intensity}; // Import Pixel struct in lib namespace

pub mod filters;
