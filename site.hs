--------------------------------------------------------------------------------
{-# LANGUAGE OverloadedStrings #-}
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

    match "*.markdown" $ content "templates/default.html"

    match "en/*.markdown" $ content "templates/default_en.html"
    
    match "templates/*" $ compile templateCompiler


siteCtx :: Context String
siteCtx =
  imageContext `mappend` 
  defaultContext

imageContext :: Context a
imageContext = field "image" $ \item -> do
  metadata <- getMetadata (itemIdentifier item)
  return $ fromMaybe "" $ mkImages <$> M.lookup "image" metadata <*> M.lookup "imagewidth" metadata

mkImages imgs width = concat $ map (\i -> mkImage i width) $ split imgs ','

mkImage img width = "<image class=\"side-image\" src=\"/img/" ++ img ++ "\" style=\"width:" ++ width ++ "\"/>"

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
