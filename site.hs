--------------------------------------------------------------------------------
{-# LANGUAGE OverloadedStrings, FlexibleContexts #-}
import Hakyll
import Control.Applicative ((<|>), (<$>), (<*>))
import Data.Monoid (mappend)
import Data.Maybe (fromMaybe)
import qualified Data.Map as M (lookup)

--------------------------------------------------------------------------------

main :: IO ()
main = hakyll $ do
    match "img/*" $ do
        route   idRoute
        compile copyFileCompiler
        
    match "js/*" $ do
        route   idRoute
        compile copyFileCompiler

    match "css/*" $ do
        route   idRoute
        compile compressCssCompiler

    match "audio/*" $ do
        route   idRoute
        compile copyFileCompiler

    match "mail/*" $ do
        route   idRoute
        compile copyFileCompiler

    match "*.php" $ do
        route   idRoute
        compile copyFileCompiler

    match "ajanvaraus.markdown" $ content "templates/ajanvaraus.html"
    
    match "*.markdown" $ content "templates/default.html"

    match "en/*.markdown" $ content "templates/default_en.html"
    
    match "templates/*" $ compile templateCompiler


siteCtx :: Context String
siteCtx =
  imageContext `mappend` 
  imagesContext `mappend` 
  defaultContext

imageContext = field "image" $ \item -> do
  metadata <- getMetadata (itemIdentifier item)
  return $ fromMaybe "" $ mkImages <$> M.lookup "image" metadata <*> M.lookup "imagewidth" metadata

imagesContext = field "imagesheight" $ \item -> do
  metadata <- getMetadata (itemIdentifier item)
  return $ fromMaybe "" $ fmap mkHeight $ M.lookup "imagesheight" metadata

mkImages imgs width = concat $ map (\i -> mkImage i width) $ split imgs ','

mkImage img width = "<image class=\"side-image\" src=\"/img/" ++ img ++ "\" style=\"width:" ++ width ++ "\"/>"

mkHeight height = "margin-top:" ++ height

content template = do
  route $ setExtension "html"
  compile $ do
    pandocCompiler
      >>= loadAndApplyTemplate template siteCtx
      >>= relativizeUrls

split :: String -> Char -> [String]
split [] delim = [""]
split (c:cs) delim
   | c == delim = "" : rest
   | otherwise = (c : head rest) : tail rest
   where
       rest = split cs delim
