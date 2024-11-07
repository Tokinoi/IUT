use crate::Image;
use crate::Pixel;

use std::mem::swap;

pub trait Filter {
    fn apply(&self, img: &mut Image);
}

pub struct Intensity {
    coefficient: f32,
}

impl Intensity {
    pub fn new(coefficient: f32) -> Self {
        Intensity { coefficient }
    }
}

impl Filter for Intensity {
    fn apply(&self, img: &mut Image) {
        for pixel in img.pixels.iter_mut() {
            pixel.r = (pixel.r as f32 * self.coefficient).round() as u8;
            pixel.g = (pixel.g as f32 * self.coefficient).round() as u8;
            pixel.b = (pixel.b as f32 * self.coefficient).round() as u8;
        }
    }
}

pub struct BlurFilter {
    size: u8,
}

impl BlurFilter {
    pub fn new(size: u8) -> Self {
        BlurFilter { size }
    }
}

impl Filter for BlurFilter {
    fn apply(&self, img: &mut Image) {
        let mut result_image = img.clone();
        for ((row, col), _pixel) in img.pixels.indexed_iter() {
            match (col, row) {
                (x, y)
                    if (1..img.pixels.cols() - 1).contains(&x)
                        && (1..img.pixels.cols() - 1).contains(&y) =>
                {
                    let mut sum_r = 0u32;
                    let mut sum_g = 0u32;
                    let mut sum_b = 0u32;
                    for i in 0..=2 {
                        for j in 0..=2 {
                            match img.pixels.get(y + j - 1, x + i - 1) {
                                Some(pixel) => {
                                    sum_r += pixel.r as u32;
                                    sum_g += pixel.g as u32;
                                    sum_b += pixel.b as u32;
                                },
                                None => {
                                    match img.pixels.get(y + j - 2, x + i - 1) {
                                        Some(pixel) => {
                                            sum_r += pixel.r as u32;
                                            sum_g += pixel.g as u32;
                                            sum_b += pixel.b as u32;
                                        },
                                        None =>{
                                            //TODO
                                        }}
                                },
                            }

                        }
                    }
                    let new_r = (sum_r / 9) as u8;
                    let new_g = (sum_g / 9) as u8;
                    let new_b = (sum_b / 9) as u8;
                    if let Some(pixel) = result_image.pixels.get_mut(y, x) {
                        *pixel = Pixel {
                            r: new_r,
                            g: new_g,
                            b: new_b,
                        };
                    } else {
                        //TODO
                    }

                }
                _ => continue,
            }
        }
        swap(img, &mut result_image);
    }
}

pub trait Convolution {
    fn kernel(&self, pixels: [Pixel; 9]) -> Pixel;
}

pub struct ApplyConvolution<F: Convolution> {
    pub convolution: F,
}

impl<F> Filter for ApplyConvolution<F>
where
    F: Convolution,
{
    fn apply(&self, img: &mut Image) {
        let mut result_image = img.clone();
        for ((row, col), pixel) in result_image.pixels.indexed_iter_mut() {
            match (col, row) {
                (x, y)
                    if (1..img.pixels.cols() - 1).contains(&x)
                        && (1..img.pixels.rows() - 1).contains(&y) =>
                {
                    let mut neighbors = [Pixel::default(); 9];
                    for j in 0..=2 {
                        for i in 0..=2 {
                            if let Some(pixel) = img.pixels.get(y + j - 1, x + i - 1) {
                                neighbors[i + j * 3] = pixel.clone();
                            } else {
                                //TODO
                            }


                        }
                    }
                    *pixel = self.convolution.kernel(neighbors);
                }
                _ => continue,
            }
        }
        swap(img, &mut result_image);
    }
}

pub struct Edges;

impl Convolution for Edges {
    fn kernel(&self, pixels: [Pixel; 9]) -> Pixel {
        let intensity_r =
            9.0 * pixels[4].r as f64 - pixels.iter().fold(0.0, |acc, p| acc + p.r as f64);
        let intensity_g =
            9.0 * pixels[4].g as f64 - pixels.iter().fold(0.0, |acc, p| acc + p.b as f64);
        let intensity_b =
            9.0 * pixels[4].b as f64 - pixels.iter().fold(0.0, |acc, p| acc + p.b as f64);
        let r = intensity_r.clamp(0.0, 255.0) as u8;
        let g = intensity_g.clamp(0.0, 255.0) as u8;
        let b = intensity_b.clamp(0.0, 255.0) as u8;

        Pixel::new(r, g, b)
    }
}

#[cfg(test)]
mod filter_tests {
    use super::*;

    #[test]
    fn intensity_on_uniform_works() {
        let color = Pixel {
            r: 50,
            g: 100,
            b: 150,
        };
        let mut uniform = Image::new(10, 5, &color);
        let intensity = Intensity::new(0.5);
        uniform.apply(&intensity);

        for pixel in uniform.pixels.iter() {
            let expected_pixel = color.mul(0.5);
            assert_eq!(expected_pixel, *pixel);
        }
    }

    #[test]
    fn intensity_one_dont_change_uniform() {
        let color = Pixel {
            r: 50,
            g: 100,
            b: 150,
        };
        let mut uniform = Image::new(10, 5, &color);
        let intensity = Intensity::new(1.0);
        uniform.apply(&intensity);

        for pixel in uniform.pixels.iter() {
            assert_eq!(*pixel, color);
        }
    }

    #[test]
    fn intensity_zero_reset_uniform() {
        let color = Pixel {
            r: 50,
            g: 100,
            b: 150,
        };
        let mut uniform = Image::new(10, 5, &color);
        let intensity = Intensity::new(0.0);
        uniform.apply(&intensity);

        for pixel in uniform.pixels.iter() {
            assert_eq!(*pixel, Pixel { r: 0, g: 0, b: 0 });
        }
    }

    #[test]
    fn blur_dont_panic_on_small_images() {
        let color = Pixel {
            r: 50,
            g: 100,
            b: 150,
        };
        let mut uniform = Image::new(2, 5, &color);
        let blur = BlurFilter::new(3);
        uniform.apply(&blur);

        for pixel in uniform.pixels.iter() {
            assert_eq!(*pixel, color);
        }

        let mut uniform = Image::new(2, 2, &color);
        let blur = BlurFilter::new(3);
        uniform.apply(&blur);

        for pixel in uniform.pixels.iter() {
            assert_eq!(*pixel, color);
        }
    }

    #[test]
    fn blur_dont_panic_on_void_images() {
        let color = Pixel {
            r: 50,
            g: 100,
            b: 150,
        };
        let mut uniform = Image::new(0, 0, &color);
        let blur = BlurFilter::new(3);
        uniform.apply(&blur);

        for pixel in uniform.pixels.iter() {
            assert_eq!(*pixel, color);
        }
    }

    #[test]
    fn blur_dont_change_uniform() {
        let color = Pixel {
            r: 50,
            g: 100,
            b: 150,
        };
        let mut uniform = Image::new(10, 5, &color);
        let blur = BlurFilter::new(3);
        uniform.apply(&blur);
let mut i=0;
        for pixel in uniform.pixels.iter() {
            assert_eq!(*pixel, color);
        }
    }
}
