--------------------------------------------------------------------------------
{-# LANGUAGE OverloadedStrings #-}
import Hakyll
import Data.Monoid (mappend)

--------------------------------------------------------------------------------

main :: IO ()
main = hakyll $ do
    match "img/*" $ do
        route   idRoute
        compile copyFileCompiler

    match "css/*" $ do
        route   idRoute
        compile compressCssCompiler

    match "*.markdown" $ content "templates/default.html"

    match "en/*.markdown" $ content "templates/default_en.html"
    
    match "templates/*" $ compile templateCompiler


siteCtx :: Context String
siteCtx = defaultContext

content template = do
  route $ setExtension "html"
  compile $ pandocCompiler
    >>= loadAndApplyTemplate template siteCtx
    >>= relativizeUrls
