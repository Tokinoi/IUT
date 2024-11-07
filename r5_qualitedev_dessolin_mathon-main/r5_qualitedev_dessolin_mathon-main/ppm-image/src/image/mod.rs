use std::fmt::{Display, Formatter};
use std::fs::File;
use std::io::{BufWriter, Write};
use std::path::Path;
use std::str::SplitWhitespace;

use grid::Grid; // 2D data structure to store image's pixels

use crate::filters::Filter;

#[derive(Clone)]
pub struct Image {
    width: usize,
    height: usize,
    pub pixels: Grid<Pixel>,
}


pub enum PPMImageError {
    IOError(String),
    InvalidFile(String),
    WriteError(String),
}



impl Image {
    pub fn new(width: usize, height: usize, color: &Pixel) -> Self {
        let pixels = Grid::init(height, width, color.clone());
        Self {
            width,
            height,
            pixels,
        }
    }

    pub fn init_rgb(width: usize, height: usize, r: u8, g: u8, b: u8) -> Self {
        let color = Pixel { r, g, b };
        Self::new(width, height, &color)
    }

    pub fn black(width: usize, height: usize) -> Self {
        Self::init_rgb(width, height, 0, 0, 0)
    }

    pub fn white(width: usize, height: usize) -> Self {
        Self::init_rgb(width, height, 255, 255, 255)
    }

    pub fn write_to_file(&self, path: &str) -> Result<(),PPMImageError> {
        let f = match File::create(path) {
            Ok(s) => { s }
            Err(e) => { Err(PPMImageError::WriteError("".to_string())) }?
        };

        let mut writer = BufWriter::new(f);

        let header = format!("P3\n{}\n{}\n255\n", self.width, self.height);

        match writer.write_all(header.as_bytes()) {
            Ok(s) => { s }
            Err(_) => { Err(PPMImageError::WriteError("".to_string()))}?
        };

        for p in self.pixels.iter() {
            match writer.write_all(p.to_string().as_bytes()) {
                Ok(s) => { s }
                Err(_) => {Err(PPMImageError::WriteError("".to_string())) }?
            };
        }
        return Result::Ok(());
    }

    pub fn load_from_file(path: &Path) -> Result<Self,PPMImageError> {
        // Load all file content in memory at once, we have at least 4Go of RAM nowadays...
        let file_content = std::fs::read_to_string(path).unwrap();
        let mut parts = file_content.split_whitespace();

        let width = parts.next().unwrap().parse().unwrap();
        if width== 0 {
            return Err(PPMImageError::InvalidFile("invalid height".to_string()));
        }

        let height = parts.next().unwrap().parse().unwrap();
        if height == 0 {
            return Err(PPMImageError::InvalidFile("invalid height".to_string()));
        }
        let max_value: u8 = parts.next().unwrap().parse().unwrap();
        if max_value == 0 {
            return Err(PPMImageError::InvalidFile("invalid max value".to_string()));
        }

        let mut image = Self::black(width, height);
        for p in image.pixels.iter_mut() {
            let r = Self::retrieve_color(&mut parts)?;

            let g = Self::retrieve_color(&mut parts)?;

            let b = Self::retrieve_color(&mut parts)?;

            *p = Pixel { r, g, b };
        }


        return Result::Ok(image);
    }

    fn retrieve_color(parts: &mut SplitWhitespace) -> Result<u8, PPMImageError> {
        parts.next()
            .ok_or_else(|| PPMImageError::InvalidFile("Missing pixel value".to_string()))?
            .parse()
            .map_err(|_| PPMImageError::InvalidFile("Invalid pixel value".to_string()))
    }


    pub fn apply<F: Filter>(&mut self, filter: &F) -> &mut Self {
        filter.apply(self);
        self
    }
}

#[derive(Default, Clone, Eq, PartialEq, Debug, Copy)]
pub struct Pixel {
    pub r: u8,
    pub g: u8,
    pub b: u8,
}

impl Pixel {
    pub fn new(r: u8, g: u8, b: u8) -> Self {
        Pixel { r, g, b }
    }

    pub fn mul(&self, f: f64) -> Self {
        Self {
            r: (f * self.r as f64).round().clamp(0.0, 255.0) as u8,
            g: (f * self.g as f64).round().clamp(0.0, 255.0) as u8,
            b: (f * self.b as f64).round().clamp(0.0, 255.0) as u8,
        }
    }
}

impl Display for Pixel {
    fn fmt(&self, f: &mut Formatter<'_>) -> std::fmt::Result {
        write!(f, "{} {} {} ", self.r, self.g, self.b)
    }
}


#[cfg(test)]
mod creation_tests {
    use super::*;

    #[test]
    fn init_rgb_is_working() {
        let _img = Image::init_rgb(7, 5, 200, 0, 0);
        assert_eq!(_img.width, 7);
        assert_eq!(_img.height, 5);
        assert_eq!(_img.pixels.cols(), 7);
        assert_eq!(_img.pixels.rows(), 5);
        for pixel in _img.pixels.iter() {
            assert_eq!(pixel.r, 200);
            assert_eq!(pixel.g, 0);
            assert_eq!(pixel.b, 0);
        }
    }

    #[test]
    fn init_rgb_zero_width_is_working() {
        let _img = Image::init_rgb(0, 7, 200, 0, 0);
        assert!(false);
    }

    #[test]
    fn init_rgb_zero_height_is_working() {
        let _img = Image::init_rgb(7, 0, 200, 0, 0);
        assert!(false);
    }

    #[test]
    fn init_rgb_zero_sizes_is_working() {
        let _img = Image::init_rgb(0, 0, 200, 0, 0);
        assert!(false);
    }

    #[test]
    fn white_is_white() {
        let white = Image::white(5, 5);
        for pixel in white.pixels.iter() {
            assert_eq!(pixel.r, 255);
            assert_eq!(pixel.g, 255);
            assert_eq!(pixel.b, 255);
        }
    }

    #[test]
    fn black_is_black() {
        let black = Image::black(5, 5);
        for pixel in black.pixels.iter() {
            assert_eq!(pixel.r, 0);
            assert_eq!(pixel.g, 0);
            assert_eq!(pixel.b, 0);
        }
    }
}

#[cfg(test)]
mod loading_tests {
    use super::*;

    #[test]
    fn load_is_working() {
        let _img = Image::load_from_file(Path::new("existing.ppm"));
        assert!(false);
    }

    #[test]
    #[should_panic]
    fn load_invalid_type_fails() {
        let _img = Image::load_from_file(Path::new("invalid_type.ppm"));
    }

    #[test]
    #[should_panic]
    fn load_invalid_sizes_fails() {
        let _img = Image::load_from_file(Path::new("zero_width.ppm"));
    }
}

#[cfg(test)]
mod saving_tests {
    use std::ptr::read;
    use super::*;
    use grid::grid;

    #[test]
    fn save_is_working() {
        let img_path = "test_image.ppm";
        let mut img = Image::black(3, 2);

        let desired_pixels = grid![[Pixel::new(0,2,3), Pixel::new(3,4,5), Pixel::new(6,7,8)]
        [Pixel::new(10,12,13), Pixel::new(23,24,25), Pixel::new(36,37,38)]
        ];

        img.pixels = desired_pixels.clone();

        img.write_to_file(img_path);

        let read_result = std::fs::read_to_string(img_path);
        if let Err(ref e) = read_result {
            println!("Error: {e}")
        }
        assert!(read_result.is_ok());

        let file_content;
        match read_result {
            Ok(ok)=>{
                 file_content = ok;
            },
            Err(e) =>{
                file_content = String::new();
            }
        }



        let mut values = file_content.split_whitespace();
        if let Some(line) = values.next() {
            assert_eq!(line.trim(), "P3");
        }
        if let Some(line) = values.next() {
            assert_eq!(line.trim(), "3");
        }
        if let Some(line) = values.next() {
            assert_eq!(line.trim(), "2");
        }
        if let Some(line) = values.next() {
            assert_eq!(line.trim(), "255");
        }
        let mut read_pixels = Grid::<Pixel>::new(2, 3);
        for pixel in read_pixels.iter_mut() {
            let mut new_pixel = [0u8; 3];
            for c in new_pixel.iter_mut() {
                let v = match values.next().map(|s| s.parse::<u8>()) {
                    Some(Ok(parsed_value)) => parsed_value,
                    _ => {
                        // Gérer le cas où la valeur ne peut pas être analysée ou s'il n'y a pas de valeur
                        // Par exemple, afficher un message d'erreur ou prendre une autre action appropriée
                        // Pour l'instant, nous assignons une valeur par défaut de 0 à `v`
                        0
                    }
                };

                *c = v;
            }
            pixel.r = new_pixel[0];
            pixel.g = new_pixel[1];
            pixel.b = new_pixel[2];
        }

        assert_eq!(desired_pixels, read_pixels);
    }
}
