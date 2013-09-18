//
//  CateViewController.m
//  ipapp
//
//  Created by kelaocai on 13-9-18.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "CateViewController.h"
#import "SubCateViewController.h"
@interface CateViewController ()

@property (strong, nonatomic) SubCateViewController *subVc;
@property (strong, nonatomic) NSDictionary *currentCate;

@end




@implementation CateViewController

@synthesize cates=_cates;



//读取首页板块目录
-(NSArray *)cates
{
    if (_cates == nil){
        
        NSURL *url = [[NSBundle mainBundle] URLForResource:@"cates" withExtension:@"plist"];
        _cates = [[NSArray arrayWithContentsOfURL:url] retain];
        
    }
    
    return _cates;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    //self.cateTableView.backgroundColor=[UIColor colorWithPatternImage:[UIImage imageNamed:@"tmall_bg_furley.png"]];
    self.navigationItem.title=@"热门板块";
    
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)dealloc {
    [_cateTableView release];
    [_cates release];
    [_currentCate release];
    [_subVc release];
    [super dealloc];
}

#pragma mark
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return 4;
}



- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *CellIdentifier = @"cate_cell";
    
    CateTableCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    
    if (cell == nil) {
        cell = [[[CateTableCell alloc] initWithStyle:UITableViewCellStyleSubtitle
                                     reuseIdentifier:CellIdentifier] autorelease];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        
    }
    //首页板块cell
    NSDictionary *cate=[self.cates objectAtIndex:[indexPath row]];
    cell.title.text=[cate objectForKey:@"name"];
    NSArray *subitems=[cate objectForKey:@"subitems"];
    NSMutableArray *subTitles=[[NSMutableArray alloc] init];
    //图标
    cell.logo.image=[UIImage imageNamed:[[cate objectForKey:@"img"] stringByAppendingString:@".png"]];
    //子板块
    for(int i=0;i<MIN(4, subitems.count);i++){
        [subTitles addObject:[[subitems objectAtIndex:i] objectForKey:@"name"]];
    }
    cell.subTtile.text=[subTitles componentsJoinedByString:@" / "];
    
    
    [subTitles release];
    
    return cell;


}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    //init subCateVc
    SubCateViewController *subVc = [[[SubCateViewController alloc]
                                     initWithNibName:NSStringFromClass([SubCateViewController class])
                                     bundle:nil] autorelease];
    NSDictionary *cate = [self.cates objectAtIndex:indexPath.row];
    subVc.subCates = [cate objectForKey:@"subitems"];
    self.currentCate = cate;
    subVc.cateVc = self;
    
    self.cateTableView.scrollEnabled = NO;
    UIFolderTableView *folderTableView = (UIFolderTableView *)tableView;
    [folderTableView openFolderAtIndexPath:indexPath WithContentView:subVc.view
                                 openBlock:^(UIView *subClassView, CFTimeInterval duration, CAMediaTimingFunction *timingFunction){
                                     // opening actions
                                 }
                                closeBlock:^(UIView *subClassView, CFTimeInterval duration, CAMediaTimingFunction *timingFunction){
                                    // closing actions
                                }
                           completionBlock:^{
                               // completed actions
                               self.cateTableView.scrollEnabled = YES;
                           }];
    
}

-(void)subCateBtnAction:(UIButton *)btn
{
    
    NSDictionary *subCate = [[self.currentCate objectForKey:@"subitems"] objectAtIndex:btn.tag];
    NSString *name = [subCate objectForKey:@"name"];
    UIAlertView *Notpermitted=[[UIAlertView alloc] initWithTitle:@"子类信息"
                                                         message:[NSString stringWithFormat:@"名称:%@, ID: %@", name, [subCate objectForKey:@"name"]]
                                                        delegate:nil
                                               cancelButtonTitle:@"确认"
                                               otherButtonTitles:nil];
    [Notpermitted show];
    [Notpermitted release];
}


@end
